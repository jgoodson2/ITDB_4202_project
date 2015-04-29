<html>
<head>
    <script>
        function ShowCompleted() {
            document.getElementById("CompleteDiv").style.display = "";
            document.getElementById("ActiveDiv").style.display = "none";
            document.getElementById("FutureDiv").style.display = "none";
        }

        function ShowActive() {
            document.getElementById("CompleteDiv").style.display = "none";
            document.getElementById("ActiveDiv").style.display = "";
            document.getElementById("FutureDiv").style.display = "none";
        }

        function ShowFuture() {
            document.getElementById("CompleteDiv").style.display = "none";
            document.getElementById("ActiveDiv").style.display = "none";
            document.getElementById("FutureDiv").style.display = "";
        }
    </script>
    <link rel="stylesheet" href="project.css"/>
</head>
<body>
<h1>Engagement Search</h1>

<form action="results.php" method="post">
    <h3>Topic Area:</h3>
    <input type="text" name="txt_topic_area"><br/>
    <?php
    /**
     * Created by IntelliJ IDEA.
     * User: Jonathon
     * Date: 4/26/2015
     * Time: 11:22 AM
     */
    $conn = oci_connect('jgoodson2', 'datadawg', '168.28.51.8/csuit');
    if (!$conn) {
        $e = oci_error();
        echo "fail";
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }
    //echo '<form action="index4.php" method="post">';


    ?>
    <!-- academic discipline dropdown -->
    <h3>Academic Discipline</h3>
    <select name="acadDisc">
        <?php
        $st_acadDisc_sql = "SELECT 0 as DISC_ID, 'Select...' as DISC_NAME, 1 SortOrder from dual
  UNION ALL
  SELECT A.DISC_ID, A.DISC_NAME, 2 SortOrder
  FROM ACAD_DISC A
  order by SortOrder, DISC_NAME";
        $st_acadDisc = oci_parse($conn, $st_acadDisc_sql);
        oci_execute($st_acadDisc);
        while (($row = oci_fetch_assoc($st_acadDisc)) != false) {
            echo '<option value="' . $row['DISC_ID'] . '"> ' . $row['DISC_NAME'] . '</option>';
        }
        oci_free_statement($st_acadDisc);
        ?>
    </select>
    <br/><br/>
    <table>
        <tr>
            <td style="width: 33%">
                <?php
                //Student level checkboxes
                echo '<h3>Student Level</h3>';
                $st_stu_lvl_sql = 'SELECT a.stu_lvl_code, a.STU_LVL_DESC FROM val_stu_lvl a ORDER BY 2 DESC';
                $st_stu_lvl = oci_parse($conn, $st_stu_lvl_sql);
                oci_execute($st_stu_lvl);
                while (($row = oci_fetch_assoc($st_stu_lvl)) != false) {
                    echo '<input type="checkbox" name ="student_lvl[]" value="' . $row['STU_LVL_CODE'] . '"> ' . $row['STU_LVL_DESC'] . '</input><br/>';
                }
                oci_free_statement($st_stu_lvl);
                ?>
            </td>
            <td style="width: 33%"><?php
                //Timeframe checkboxes
                echo '<h3>Time Frame</h3>';
                $st_timeframe_sql = 'SELECT A.TIMEFR_ID, A.TIMEFR_DESC FROM VAL_TIMEFRAME A ORDER BY 1 ASC';
                $st_timeframe = oci_parse($conn, $st_timeframe_sql);
                oci_execute($st_timeframe);
                while (($row = oci_fetch_assoc($st_timeframe)) != false) {
                    echo '<input type="checkbox" name ="timeframe[]" value="' . $row['TIMEFR_ID'] . '"> ' . $row['TIMEFR_DESC'] . '</input><br/>';
                }
                oci_free_statement($st_timeframe);
                ?>
            </td>
            <td style="width: 33%">
                <!--engagement type-->
                <?php
                echo '<h3>Engagement Type</h3>';
                $st_engtype_sql = 'select A.TYPE_ID, A.TYPE_DESC from VAL_ENG_TYPE A order by A.TYPE_DESC ASC';
                $st_engtype = oci_parse($conn, $st_engtype_sql);
                oci_execute($st_engtype);
                while (($row = oci_fetch_assoc($st_engtype)) != false) {
                    echo '<input type="checkbox" name ="engtype[]" value="' . $row['TYPE_ID'] . '"> ' . $row['TYPE_DESC'] . '</input><br/>';
                }
                oci_free_statement($st_engtype);
                ?>

            </td>
        </tr>
    </table>

    <!-- future/active/completed -->
    <h3>Future, Active, or Completed?</h3>
    <table style="text-align:left; color: #000; width:100%; margin-top:10px;">
        <tr>
            <td style="width:33%;" nowrap><input type="radio" name="status" value="future" id="Future"
                                                 onClick="ShowFuture();"> Future Engagements
            </td>
            <td style="width:34%;" nowrap><input type="radio" name="status" value="active" id="Active"
                                                 onClick="ShowActive();"> Active Engagements
            </td>
            <td style="width:33%;" nowrap><input type="radio" name="status" value="completed" id="Completed"
                                                 onClick="ShowCompleted();"> Completed Engagements
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div id="CompleteDiv" style="display:none; font-style:italic;">&nbsp;&nbsp;* This will filter for
                    engagements that have an end date before today.
                </div>
                <div id="ActiveDiv" style="display:none; font-style:italic;">&nbsp;&nbsp;* This will filter for
                    engagements that have a start date before today, and an end date after today.
                </div>
                <div id="FutureDiv" style="display:none; font-style:italic;">&nbsp;&nbsp;* This will filter for
                    engagements that have a start date after today.
                </div>
            </td>
        </tr>
    </table>
    <br/>
    <!-- UNDER DEVELOPMENT -->
    <!--
        <h2>Under Development:</h2>
        <h3>On/Off Campus</h3>
        <br/>
        <input type="radio" name="location" value="onCampus">On Campus<br/>
        <input type="radio" name="location" value="offCampus">Off Campus
        //-->
    <?php
    echo '<br/><input type="submit">';
    echo '<input type="reset">';
    echo '</form>';

    oci_close($conn);

    ?>
</body>
</html>


