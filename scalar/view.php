<html>
    <head>
        <meta charset="UTF-8">
        <title>HR DB</title>
        <link rel="stylesheet" type="text/css" href="http://localhost/scalar/css/style1.css">
    </head>
    <body>
        <?php
        $department = '';
        $position = '';
        $slectedpagenum = 0;
        $config = parse_ini_file('./config/config.ini', 'xml');

        $str = ($_SERVER['SERVER_NAME'] == 'localhost') ? '/scalar/' : '/';

        if ($_SERVER['REQUEST_URI'] != $str) {
            try {
                $terms = array();
                $deps = SY::getDepartmentsArray(); //departments
                $poss = SY::getPositionsArray(); //positions

                $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $uri_parts = explode('/', trim($url_path, ' /'));
                $to_skip = array('scalar', 'index.php', 'employees');
                foreach ($uri_parts as $part) {
                    if (!in_array($part, $to_skip, true)) {
                        $terms[] = $part;
                    }
                }

                $part1 = array_shift($terms);
                if (isset($part1)) {
                    if (in_array(strtolower($part1), $deps)) {
                        $department = $part1;
                    } else {
                        if (ctype_digit($part1)) {
                            $slectedpagenum = $part1 - 1;
                        } else {
                            throw new Exception();
                        }
                    }
                }
                $part2 = array_shift($terms);
                if (isset($part2)) {
                    if (in_array(strtolower($part2), $poss)) {
                        $position = $part2;
                    } else {
                        if (ctype_digit($part2)) {
                            $slectedpagenum = $part2 - 1;
                        } else {
                            throw new Exception();
                        }
                    }
                }
                $part3 = array_shift($terms);
                if (isset($part3)) {
                    if (ctype_digit($part3)) {
                        $slectedpagenum = $part3 - 1;
                    } else {
                        throw new Exception();
                    }
                }
            } catch (Exception $ex) {
                header('HTTP/1.0 404 Not Found*');
                exit('404 Not Found*');
            }
        }
        ?>
        <?php
        if (isset($_POST['department'])) {
            $department = $_POST['department'];
        }
        if ($config['xml']['xml_create'] == 'true') {
            SY::exportToXML($department, $position);
        }
        if ($config['xml']['xml_connection'] == 'true') {
            $xml = new Db_xml();
            $allrows = $xml->select($department, $position);
        } else {
            $allrows = SY::getAll($department, $position);
        }
        $alldep = SY::getDepartmentsInUse();

        if (!$allrows) {
            echo "SQL query failed";
            exit();
        } else {
            if (isset($_POST['tablelength'])) {
                $rowsperpage = $_POST['tablelength'];
            } else {
                $rowsperpage = 20;
            }

            $rowsnumber = count($allrows);
            $pager = new Pager($rowsnumber, $rowsperpage);
            ?>
            <div class="main">
                <div class="rowcol">
                    <div class="column">
                        <form name="departmentform" id="departmentform" method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
                            <label for="departments">Department</label>
                            <select name="department" id="department">
                                <option value="" >Any department</option>
                                <?php
                                foreach ($alldep as $key => $dep) {
                                    echo '<option value="' . $dep['department'] . '">' . $dep['department'] . '</option>';
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                    <div class="column">
                        <form name="rowsperpageform" id="rowsperpageform" method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
                            <label for="tablelength">Records per page</label>
                            <select name="tablelength" id="tablelength">
                                <option value="20" >20</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </form>
                    </div>
                </div>
                <?php
                $pager->pageSelector();
                ?>  
                <div class="scroll">
                    <table class="tb0">
                        <tr>
                            <th>Full name</th>
                            <th>Birth date</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Payment</th>
                            <th>Salary</th>
                            <th>Revenue</th>
                        </tr>
                        <?php
                        $emp = new EmployeeH();
                        $revenue = 0;
                        foreach ($allrows as $key => $row) {
                            if ($row['salarytype'] == 0) {
                                $emp->setSalary($row['salary']);
                                $revenue = $emp->getSalary();
                            } else {
                                $revenue = $row['salary'];
                            }
                            ?><?php
                            $salarytype = $row['salarytype'] == 0 ? 'Hourly' : 'Monthly';
                            echo '<tr class = "row">' .
                            '<td>' . $row['fullname'] . '</td>' .
                            '<td>' . $row['birthdate'] . '</td>' .
                            '<td>' . $row['department'] . '</td>' .
                            '<td>' . $row['position'] . '</td>' .
                            '<td>' . $salarytype . '</td>' .
                            '<td>' . $row['salary'] . '</td>' .
                            '<td>' . $revenue . '</td>' . '</tr>';
                            ?>



                            <?php
                        }
                        ?>

                    </table>
                </div>
            </div>
            <?php
        }
        ?>
        <script>
            var rowsperpage = <?php echo $rowsperpage; ?>;
            var rowsnumber = <?php echo $rowsnumber; ?>;
            var slectedpagenum = <?php echo isset($slectedpagenum) ? $slectedpagenum : 0; ?>;
            window.onload = doSmth();

            function doSmth() {


                var spnum;
                var aa = Array();
                aa = document.getElementsByClassName('ps');
                for (var i = 0; i < aa.length; i++) {
                    aa[i].addEventListener("click", selPage);
                }
                aa[slectedpagenum].click();
                document.getElementById("leftarrow").addEventListener("click", leftArrow);
                document.getElementById("rightarrow").addEventListener("click", rightArrow);
                document.getElementById("tablelength").addEventListener("blur", tableLength);
                document.getElementById("department").addEventListener("blur", selDepartment);
                showRowsNumber();
            }

            function showRowsNumber() {
                var options = document.getElementById("tablelength").options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == rowsperpage) {
                        document.getElementById("tablelength").selectedIndex = i;
                        break;
                    }
                }
                console.log(options);
            }
            function selDepartment(event) {
                console.log(event.currentTarget.value);
                document.getElementById('departmentform').submit();
            }

            function tableLength(event) {
                console.log(event.currentTarget.value);
                document.getElementById('rowsperpageform').submit();
            }
            function leftArrow() {
                console.log(leftArrow);
                spnum = spnum - 5;
                showSelectors(spnum);
            }

            function rightArrow() {
                console.log(rightArrow);
                spnum = spnum + 5;
                showSelectors(spnum);
            }

            function selPage(event) {

                slectedpagenum = parseInt(event.currentTarget.getAttribute('data-pagenumber'), 10);
                spnum = slectedpagenum;
                var trs = Array();
                trs = document.getElementsByClassName('row'); //all rows
                var rowfrom = slectedpagenum * rowsperpage;
                var rowto = slectedpagenum * rowsperpage + rowsperpage - 1;
                for (var i = 0; i < rowsnumber; i++) {
                    trs[i].classList.add((i % 2 == 0) ? 'even' : "odd");
                    trs[i].classList.remove("show");
                    trs[i].classList.add("hide");
                    if (i >= rowfrom && i <= rowto) {
                        trs[i].classList.remove("hide");
                        trs[i].classList.add("show");
                    }
                }
                showSelectors(slectedpagenum);
            }
            function  showSelectors(spnum) {

                var slpfrom = spnum - 5;
                var slpto = spnum + 5;

                aa = document.getElementsByClassName('ps');
                for (var i = 0; i < aa.length; i++) {

                    aa[i].classList.remove("showps");
                    aa[i].classList.add("hide");
                    if (spnum == slectedpagenum) {
                        aa[i].classList.remove("selected");
                        if (spnum == i) {
                            aa[i].classList.add("selected");
                        }
                    }
                    if (spnum > 5) {
                        if (i >= slpfrom && i <= slpto) {
                            aa[i].classList.remove("hide");
                            aa[i].classList.add("showps");
                        }
                    }
                    if (spnum <= 5) {
                        if (i >= 0 && i <= 10) {
                            aa[i].classList.remove("hide");
                            aa[i].classList.add("showps");
                        }

                    }


                }

            }

        </script>




    </body>
</html>
