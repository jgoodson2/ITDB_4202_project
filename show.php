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
/**
 * Created by IntelliJ IDEA.
 * User: Jonathon
 * Date: 4/29/2015
 * Time: 12:50 PM
 */

//echo '<h3>var dump:</h3>';
//var_dump($_GET);

$conn = oci_connect('jgoodson2', 'datadawg', '168.28.51.8/csuit');
if (!$conn) {
    $e = oci_error();
    echo "fail";
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

//echo '<h3>sql run:</h3>';
$sql = 'SELECT * FROM V_SEARCH A WHERE A.ENG_ID = ' . $_GET['engid'];
//echo $sql;

$st_showeng = oci_parse($conn, $sql);
oci_execute($st_showeng);
while (($row = oci_fetch_assoc($st_showeng)) != false) {
    $html = '';
    $html .= '<h1> ' . $row['ENG_NAME'] . '</h1>';
    $html .= "Website: <a href='" . $row['ENG_WEBSITE'] . "'>" . $row['ENG_WEBSITE'] . "</a>";
    $html .= '<h3> Description: </h3>';
    $html .= "<p class ='details'>" . $row['ENG_DESC'] . '</p>';
    $html .= '<h3> Details: </h3>';
    $html .= '<p>' . $row['ENG_DETAILS'] . '</p>';
    $html .= 'Start Date: ' . $row['ENG_START_DATE'];
    $html .= '<br/>End Date: ' . $row['ENG_END_DATE'];
    $html .= '<br/>Type: ' . $row['TYPE_DESC'];
    $html .= '<br/>Student Level: ' . $row['STU_LVL_DESC'];
    $html .= '<br/>Time Frame: ' . $row['TIMEFR_DESC'];
    $html .= '<br/>';
}
oci_free_statement($st_showeng);

//display organization info
$html .= '<h3>Host Organizations</h3>';
$st_orginfo_sql = "SELECT *
      FROM engorg a
      JOIN ORGANIZATION b
      ON a.ORG_ID    = b.ORG_ID
      WHERE a.ENG_ID = " . $_GET['engid'] . "
      ORDER BY DECODE(lower(a.PRINCIPLE_ORG), 'y', 0, 'n', 1), b.ORG_NAME";
$st_orginfo = oci_parse($conn, $st_orginfo_sql);
oci_execute($st_orginfo);
while (($row = oci_fetch_assoc($st_orginfo)) != false) {
    $html .= $row['ORG_NAME'];
    if(($row['PRINCIPLE_ORG']=='Y')||($row['PRINCIPLE_ORG']=='y')){
        $html .= "<span class='principle_org'>(Principle Organization)</span>";
    }
    $html .= '<br/>';
    $html .= "<span class='org_link'><a href='http://".$row['ORG_WEBSITE']."'>".$row['ORG_WEBSITE'].'</a></span>';
    $html .= '';

    $html .= "<br/>";
}
oci_free_statement($st_orginfo);

//display assessment info
$html .= '<h3>Assessment</h3>';
$st_assesinfo_sql = "SELECT ENG_ID,
  CLASS_CRN,
  CLASS_TERM,
  ASSESSMENT,
  ASSESSMENT_DATE,
  STUDENT_PARTICIPANTS
FROM ENGCLASS
WHERE ENG_ID = ".$_GET['engid']."ORDER BY 1";
$st_assessinfo = oci_parse($conn, $st_assesinfo_sql);
oci_execute($st_assessinfo);
while (($row = oci_fetch_assoc($st_assessinfo)) != false) {
    $html .= '<span style="font-weight: 300">'.'CRN: '.$row['CLASS_CRN'].'</span><br/>';
    $html .= 'Term: '.$row['CLASS_TERM'].'<br/>';
    $html .= 'Student Participants: '.$row['STUDENT_PARTICIPANTS'].'<br/>';
    $html .= 'Assessment Date: '.$row['ASSESSMENT_DATE'].'<br/>';
    $html .= 'Assessment: '.$row['ASSESSMENT'].'<br/>';

    $html .= "<br/>";
}
oci_free_statement($st_assessinfo);




echo $html;

?>
</div>
</body>
</html>
