<?php

namespace App\Controller;

use App\Model\xmlConverterModel;
use App\Model\jsonConverterModel;

class home extends Controller {

    public function index() {
        $result = jsonConverterModel::encode
        (
        	xmlConverterModel::decode(
        		file_get_contents (ROOT_DIR . '\data.xml')
        	)
        );

        $this->view('home/index', $result);
    }

}