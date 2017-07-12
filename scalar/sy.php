<?php

class SY {

    public static $day_hours = 8;

    public static function getDepartmentsInUse() {
        require './sql/departmentsinuse.php';
        $db = new Db();
        $rows = $db->select($sql1);
        return $rows;
    }

    public static function getDepartmentsArray() {
        require './sql/departmentsinuse.php';
        $db = new Db();
        $rows = $db->select($sql1);
        $rows1 = array();
        foreach ($rows as $key => $row) {
            $rows1[] = strtolower($row['department']);
        }
        return $rows1;
    }

    public static function getPositionsArray() {
        require './sql/positionsinuse.php';
        $db = new Db();
        $rows = $db->select($sql1);
        $rows1 = array();
        foreach ($rows as $key => $row) {
            $rows1[] = trim(strtolower($row['position']));
        }
        return $rows1;
    }

    public static function getAll($department = '', $position = '') {
        $pc = new parse_class;
        $pc->get_tpl('./sql/allemployees.sql');
        $pc->set_tpl('param1', $department);
        $pc->set_tpl('param2', $position);
        $sql1 = $pc->tpl_parse();

        $db = new Db();
        $rows = $db->select($sql1);
        return $rows;
    }

// work days in month
    public static function countDays($year, $month, $ignore) {
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {
            if (in_array(date("w", $counter), $ignore) == false) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        return $count;
    }

//work hours for every month in the selected year
    public static function workHoursInMonth($year, $day_hours) {
        $work_hours = array();
        for ($month = 1; $month < 13; $month++) {
            $days = SY::countDays($year, $month, array(0, 6));
            $work_hours[] = $days * $day_hours;
        }
        return $work_hours;
    }

    public static function exportToXML($department = '', $position = '') {

        $txt = '';
        $rows = SY::getAll($department, $position);
        $txt .= "<?xml version='1.0' standalone='yes'?>";
        $txt .= '<employees>';

        foreach ($rows as $key => $row) {
            if ($department != '') {

                if (strtolower($row['department']) == strtolower($department)) {

                    if ($position != '') {
                        if (strtolower($row['position']) == $position) {
                            $txt .= '<employee>';
                            $txt .= '<fullname>' . $row['fullname'] . '</fullname>';
                            $txt .= '<birthdate>' . $row['birthdate'] . '</birthdate>';
                            $txt .= '<department>' . $row['department'] . '</department>';
                            $txt .= '<position>' . $row['position'] . '</position>';
                            $txt .= '<salarytype>' . $row['salarytype'] . '</salarytype>';
                            $txt .= '<salary>' . $row['salary'] . '</salary>';

                            $txt .= '</employee>';
                        }
                    } else {
                        $txt .= '<employee>';
                        $txt .= '<fullname>' . $row['fullname'] . '</fullname>';
                        $txt .= '<birthdate>' . $row['birthdate'] . '</birthdate>';
                        $txt .= '<department>' . $row['department'] . '</department>';
                        $txt .= '<position>' . $row['position'] . '</position>';
                        $txt .= '<salarytype>' . $row['salarytype'] . '</salarytype>';
                        $txt .= '<salary>' . $row['salary'] . '</salary>';

                        $txt .= '</employee>';
                    }
                }
            } else {


                $txt .= '<employee>';
                $txt .= '<fullname>' . $row['fullname'] . '</fullname>';
                $txt .= '<birthdate>' . $row['birthdate'] . '</birthdate>';
                $txt .= '<department>' . $row['department'] . '</department>';
                $txt .= '<position>' . $row['position'] . '</position>';
                $txt .= '<salarytype>' . $row['salarytype'] . '</salarytype>';
                $txt .= '<salary>' . $row['salary'] . '</salary>';

                $txt .= '</employee>';
            }
        }
        $txt .= '</employees>';
        $xmlfile = "./xml/employees.xml";
        $fh = fopen($xmlfile, 'w') or die("can't open file");
        fwrite($fh, $txt);
        fclose($fh);
    }

    public static function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

}

class parse_class {

    var $vars = array();
    var $template;

    function get_tpl($tpl_name) {
        if (empty($tpl_name) || !file_exists($tpl_name)) {
            return false;
        } else {
            $this->template = file_get_contents($tpl_name);
            return true;
        }
    }

    function set_tpl($key, $var) {
        $this->vars['{' . $key . '}'] = $var;
    }

    function tpl_parse() {
        foreach ($this->vars as $find => $replace) {
            $this->template = str_replace($find, $replace, $this->template);
        }
        return $this->template;
    }

}
