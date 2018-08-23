<?php

namespace App\Model;

class xmlConverterModel extends Model {

    public static function decode ($contents) {

        if (!function_exists('xml_parser_create') || $contents === null) {
            return array();
        }

        $parser = xml_parser_create();

        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents) , $xml_values);
        xml_parser_free($parser);

        if ($xml_values === null) {
        	return ;
        }

        // Initializations
        $xml_array = array();
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        $current = & $xml_array; //Refference

        foreach($xml_values as $data) {
            $result = array();
            $attributes_data = array();

            if (isset($data['value'])) {
            	$result = $data['value'];
            }

            // Set the attributes
            if (isset($data['attributes'])) {
                foreach($data['attributes'] as $attr => $val) {
                    if ( $attr == 'ResStatus' ) {
                        $current[$attr][] = $val;
                    }

                    $attributes_data[$attr] = $val;
                }
            }

            // Check tag status and create nested array
            if ($data['type'] == 'open') {
                $parent[$data['level'] - 1] = & $current;

                if (!is_array($current) || !(in_array($data['tag'], array_keys($current)))) {
                    $current[$data['tag']] = $result;

                    if ($attributes_data) {
                    	$current[$data['tag'] . '_attr'] = $attributes_data;
                    }

                    $repeated_tag_index[$data['tag'] . '_' . $data['level']] = 1;
                    $current = & $current[$data['tag']];
                } else {
                    if (isset($current[$data['tag']][0])) { //If there is a 0th element it is already an array
                        $current[$data['tag']][$repeated_tag_index[$data['tag'] . '_' . $data['level']]] = $result;
                        $repeated_tag_index[$data['tag'] . '_' . $data['level']]++;
                    } else {

                    	//This section will make the value an array if multiple tags with the same name appear together
                        $current[$data['tag']] = array(
                            $current[$data['tag']],
                            $result
                        );

                        $repeated_tag_index[$data['tag'] . '_' . $data['level']] = 2;

                        if (isset($current[$data['tag'] . '_attr'])) {
                            $current[$data['tag']]['0_attr'] = $current[$data['tag'] . '_attr'];
                            unset($current[$data['tag'] . '_attr']);
                        }
                    }

                    $last_item_index = $repeated_tag_index[$data['tag'] . '_' . $data['level']] - 1;
                    $current = & $current[$data['tag']][$last_item_index];
                }
            } elseif ($data['type'] == 'complete') {

                // if the key is already exists add nested value otherwise define the key.
                if (!isset($current[$data['tag']])) {
                    $current[$data['tag']] = $result;
                    $repeated_tag_index[$data['tag'] . '_' . $data['level']] = 1;

                    if ($attributes_data) {
                    	$current[$data['tag'] . '_attr'] = $attributes_data;
                    }
                } else {
                    if (isset($current[$data['tag']][0]) && is_array($current[$data['tag']])) {
                        $current[$data['tag']][$repeated_tag_index[$data['tag'] . '_' . $data['level']]] = $result;

                        if ($attributes_data) {
                            $current[$data['tag']][$repeated_tag_index[$data['tag'] . '_' . $data['level']] . '_attr'] = $attributes_data;
                        }

                        $repeated_tag_index[$data['tag'] . '_' . $data['level']]++;
                    } else {
                        $current[$data['tag']] = array(
                            $current[$data['tag']],
                            $result
                        );

                        $repeated_tag_index[$data['tag'] . '_' . $data['level']] = 1;

                        if (isset($current[$data['tag'] . '_attr'])) {
                            $current[$data['tag']]['0_attr'] = $current[$data['tag'] . '_attr'];
                            unset($current[$data['tag'] . '_attr']);
                        }

                        if ($attributes_data) {
                            $current[$data['tag']][$repeated_tag_index[$data['tag'] . '_' . $data['level']] . '_attr'] = $attributes_data;
                        }

                        $repeated_tag_index[$data['tag'] . '_' . $data['level']]++; //0 and 1 index is already taken
                    }
                }
            }
            elseif ($data['type'] == 'close') {
                $current = & $parent[$data['level'] - 1];
            }
        }

        return $xml_array;
    }
}