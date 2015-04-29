<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jonathon
 * Date: 4/21/2015
 * Time: 3:11 PM
 */ ?>

    <!doctype html>

    <html lang="en">
    <head>
        <meta charset="utf-8">

        <title>PACE is the Place!</title>
        <meta name="description" content="PACE is the Place">
        <meta name="author" content="Jonathon Goodson">

        <link rel="stylesheet" href="css/styles.css?v=1.0">

        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="js/scripts.js"></script>
        <link rel="stylesheet" href="project.css"/>
    </head>

    <body>
    <div id="results">
    <?php
    $whereSwitch = 0;

    $conn = oci_connect('jgoodson2', 'datadawg', '168.28.51.8/csuit');
    if (!$conn) {
        $e = oci_error();
        echo "fail";
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    $sql = 'SELECT * FROM V_SEARCH A ';

    //echo '<h3>Var_Dump:</h3>';
    //var_dump($_POST);
    //echo '<br/>';

    if ((!empty($_POST))) {

        //handle student level
        if (!empty($_POST['student_lvl'])) {
            if ($whereSwitch == 0) {
                $sql .= ' WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $stu_lvl_COUNTER = 0;
            $stu_lvl_COUNT = count($_POST['student_lvl']);
            foreach ($_POST['student_lvl'] as $selected_stu_lvl) {
                if ($stu_lvl_COUNTER == 0) {
                    $sql .= '(';
                } else {
                    $sql .= ' OR ';
                }
                $sql .= " a.stu_lvl_code = '" . $selected_stu_lvl . "' ";
                $stu_lvl_COUNTER++;
                if ($stu_lvl_COUNTER == $stu_lvl_COUNT) {
                    $sql .= ')';
                }
            }
            ++$whereSwitch;
        }
        //handle timeframe
        if (!empty($_POST['engtype'])) {
            if ($whereSwitch == 0) {
                $sql .= ' WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $sql .= ' (';
            $engtype_COUNTER = 0;
            $engtype_COUNT = count($_POST['engtype']);
            foreach ($_POST['engtype'] as $selected_engtype) {
                if ($engtype_COUNTER != 0) {
                    $sql .= ' OR ';
                }
                $sql .= " A.ENG_TYPE ='" . $selected_engtype . "' ";
                $engtype_COUNTER++;
            }
            $sql .= ')';
            ++$whereSwitch;
        }
        //handle engagement type
        if (!empty($_POST['timeframe'])) {
            if ($whereSwitch == 0) {
                $sql .= ' WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $sql .= ' (';
            $timeframe_COUNTER = 0;
            $timeframe_COUNT = count($_POST['timeframe']);
            foreach ($_POST['timeframe'] as $selected_timeframe) {
                if ($timeframe_COUNTER != 0) {
                    $sql .= ' OR ';
                }
                $sql .= " A.TIMEFR_ID ='" . $selected_timeframe . "' ";
                $timeframe_COUNTER++;
            }
            $sql .= ')';
            ++$whereSwitch;
        }

        //handle academic discipline
        if (!empty($_POST['acadDisc'])) {
            if ($whereSwitch == 0) {
                $sql .= ' WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $sql .= " INSTR(A.V8_DISC, '^" . $_POST['acadDisc'] . "^') > 0 ";
            ++$whereSwitch;
        }

        //handle topic area
        if ($_POST['txt_topic_area'] != '') {
            if ($whereSwitch == 0) {
                $sql .= ' WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $sql .= " INSTR(lower(A.V3_TOPIC_NAMES), lower('" . $_POST['txt_topic_area'] . "')) > 0";
            ++$whereSwitch;

        }

        //handle future/active/completed
        if (!empty($_POST['status'])) {
            if ($whereSwitch == 0) {
                $sql .= ' WHERE (';
            } else {
                $sql .= ' AND (';
            }
            if ($_POST['status'] == 'future') {
                $sql .= ' SYSDATE < A.ENG_START_DATE ';
            } else if ($_POST['status'] == 'active') {
                $sql .= ' (SYSDATE >= A.ENG_START_DATE)AND(SYSDATE <= A.ENG_END_DATE) ';
            } else {
                $sql .= ' SYSDATE > A.ENG_END_DATE ';
            }
            $sql .= ')';
            ++$whereSwitch;
        }

        $sql .= ' ORDER BY A.ENG_START_DATE ';
    }

    //echo '<br/><br/>';
    echo '<h3>SQL Created:</h3>';
    echo $sql;
    echo '<br/><br/>';

    echo '<h3>Results:</h3>';
    $st_results = oci_parse($conn, $sql);
    oci_execute($st_results);
    while (($row = oci_fetch_assoc($st_results)) != false) {
        echo "<a href=show.php?engid=" . $row['ENG_ID'] . ">" . $row['ENG_NAME'] . '</a>: ' . $row['ENG_DESC'] . '<br/>' .
            'Starts: ' . $row['ENG_START_DATE'] . ';   Ends: ' . $row['ENG_END_DATE'] . '<br/><br/>';
    }
    oci_free_statement($st_results);



    oci_close($conn);
    ?>
    </div>
    </body>
    </html>

    <!-- Just some code for safekeeping -->
<?php
//    if (!empty($_POST['student_lvl'])) {
//
//        $st_stu_lvl_sql = 'SELECT a.stu_lvl_code FROM val_stu_lvl a ';
//        $st_stu_lvl = oci_parse($conn, $st_stu_lvl_sql);
//        oci_execute($st_stu_lvl);
//       $stu_lvl_COUNTER = 0;
//        $stu_lvl_COUNT = count($_POST['student_lvl']); //2
//        echo 'st_stu_lvl_COUNT = ' . $stu_lvl_COUNT;
//        echo '<br/><br/>';
//        while (($row = oci_fetch_assoc($st_stu_lvl)) != false) {
//            echo 'st_stu_lvl_COUNTER = ' .$stu_lvl_COUNTER . '<br/>';//JONATHON DEBUG
//            echo 'st_stu_lvl_COUNTER == 0 is '.($stu_lvl_COUNTER == 0) . '<br/>';
//            echo "At line " . __LINE__ . '<br/>';
//            if ($stu_lvl_COUNTER == 0)
//            {
//                echo "At line " . __LINE__ . '<br/>';
//                $sql .= '(';
//            } else {
//                echo "At line " . __LINE__ . '<br/>';
//                $sql .= ' OR ';
//            }
//
//            if (in_array($row['STU_LVL_CODE'], $_POST['student_lvl'])) {
//                echo "At line " . __LINE__ . '<br/>';
//                $sql .= " a.stu_lvl_code  = '" . $row['STU_LVL_CODE'] . "' ";
//            }
//
//           $stu_lvl_COUNTER+=1;
//
//            if ($stu_lvl_COUNTER == $stu_lvl_COUNT) {
//                echo "At line " . __LINE__ . '<br/>';
//                $sql .= ')';
//            }
//            echo "At line " . __LINE__ . ' and ending loop<br/>';
//        }
//        oci_free_statement($st_stu_lvl);
//    }
//    //end of handle student level
//
?>