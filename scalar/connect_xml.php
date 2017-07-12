<?php

class Db_xml {

    protected $file = './xml/employees.xml';
    protected static $connection;

    public function connect() {

        if (file_exists($this->file)) {

            self::$connection = simplexml_load_file($this->file);
        } else {
            exit('Не удалось открыть файл employees.xml.');
        }
        return self::$connection;
    }

    public function select($department, $position) {
        $xml = $this->connect();
        $rows = array();

        $nrows = count($xml->employee);
        for ($i = 0; $i < $nrows; $i++) {

            if ($department != '') {
                if ($xml->employee[$i]->department == $department) {
                    if ($position != '') {
                        if ($xml->employee[$i]->position == $position) {
                            $rows[$i] = array();
                            $rows[$i]['fullname'] = $xml->employee[$i]->fullname;
                            $rows[$i]['birthdate'] = $xml->employee[$i]->birthdate;
                            $rows[$i]['department'] = $xml->employee[$i]->department;
                            $rows[$i]['position'] = $xml->employee[$i]->position;
                            $rows[$i]['salarytype'] = $xml->employee[$i]->salarytype;
                            $rows[$i]['salary'] = $xml->employee[$i]->salary;
                        }
                    } else {

                        $rows[$i] = array();
                        $rows[$i]['fullname'] = $xml->employee[$i]->fullname;
                        $rows[$i]['birthdate'] = $xml->employee[$i]->birthdate;
                        $rows[$i]['department'] = $xml->employee[$i]->department;
                        $rows[$i]['position'] = $xml->employee[$i]->position;
                        $rows[$i]['salarytype'] = $xml->employee[$i]->salarytype;
                        $rows[$i]['salary'] = $xml->employee[$i]->salary;
                    }
                }
            } else {
                $rows[$i] = array();
                $rows[$i]['fullname'] = $xml->employee[$i]->fullname;
                $rows[$i]['birthdate'] = $xml->employee[$i]->birthdate;
                $rows[$i]['department'] = $xml->employee[$i]->department;
                $rows[$i]['position'] = $xml->employee[$i]->position;
                $rows[$i]['salarytype'] = $xml->employee[$i]->salarytype;
                $rows[$i]['salary'] = $xml->employee[$i]->salary;
            }
        }

        return $rows;
    }

}
