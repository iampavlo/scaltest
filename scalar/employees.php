<?php

class Employee {

    protected $fn; //$firstname;
    protected $sn; //$secondname;
    protected $ln; //$lastname;
    protected $bd; //$birthdate;
    protected $dt; //$department;
    protected $pn; //$position;
    protected $st; //$salarytype;
    protected $salary;

    public function setFirstName($firstname) {
        $firstname = trim($firstname);
        if ($firstname == '') {
            throw new Exception('Укажите имя сотрудника');
        }
        $this->fn = $firstname;
    }

    public function getFirstName() {
        return $this->fn;
    }

    public function setSecondName($secondname) {
        $secondname = trim($secondname);
        if ($secondname == '') {
            throw new Exception('Укажите отчество сотрудника');
        }
        $this->sn = $secondname;
    }

    public function getSecondName() {
        return $this->sn;
    }

    public function setLastName($lastname) {
        $lastname = trim($lastname);
        if ($lastname == '') {
            throw new Exception('Укажите фамилию сотрудника');
        }
        $this->ln = $lastname;
    }

    public function getLastName() {
        return $this->ln;
    }

    public function setBirthdate($birthdate) {
        $birthdate = trim($birthdate);
        if (SY::validateDate($birthdate) == false) {
            throw new Exception('Неправильный формат даты');
        }
        $this->ln = $birthdate;
    }

    public function getBirthdate() {
        return $this->ln;
    }

    public function setDepartment($department) {
        $department = trim($department);
        if ($department == '') {
            throw new Exception('Укажите название отдела');
        }
        $this->dt = $department;
    }

    public function getDepartment() {
        return $this->dt;
    }

    public function setPosition($position) {
        $position = trim($position);
        if ($position == '') {
            throw new Exception('Укажите должность сотрудника');
        }
        $this->pn = $position;
    }

    public function getPosition() {
        return $this->pn;
    }

    public function __construct() {
        
    }

    public function getSalary() {
        return $this->salary;
    }

    public function setSalary($salary) {
        $salary = trim($salary);
        if ($salary == '') {
            throw new Exception('Укажите название отдела');
        }
        $this->dt = $salary;
    }

}



class EmployeeH extends Employee {

    protected $year;
    protected $month;
    protected $work_hours = array();

    public function __construct() {
        $this->work_hours = SY::workHoursInMonth($this->year, SY::$day_hours);
        $config = parse_ini_file('./config/config.ini', 'employees');
        $this->year = $config['employees']['year'];
        $this->month = $config['employees']['month'];
    }

    public function getSalary() {
        return $this->work_hours[$this->month - 1] * $this->salary;
    }

    public function setSalary($salary) {
        $this->salary = $salary;
    }

}
