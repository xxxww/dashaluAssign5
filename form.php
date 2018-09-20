<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Calendar Input</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript">
        function valForm() {
            var locationval = document.forms["myForm"]["location"].value;
            var eventnameval = document.forms["myForm"]["eventname"].value;
            var startval = document.forms["myForm"]["starttime"].value;
            var endval = document.forms["myForm"]["endtime"].value;
            if (((/^[A-Za-z0-9\s]+$/.test(eventnameval)) == true) && (/^[A-Za-z0-9\s]+$/.test(locationval)) == true) {
                return true;
            }
            else {
                alert("Error:Event Name and Location should accept only alphanumeric characters");
                return false;
            }
        }
    </script>
</head>

<body>
    <nav>
        <a href="calendar.php">My Calendar</a> &nbsp; &nbsp; &nbsp;
        <a href="form.php">Form Input</a>
    </nav>
    <br/>

    <?php
        require_once "utils.php";
        if (isset($_POST['submit'])) {
            if (!empty($_POST["eventname"]) && !empty($_POST["starttime"]) && !empty($_POST["endtime"]) && !empty($_POST["location"]) && !empty($_POST["day"])) {
                $event_name = $_POST["eventname"];
                $start_time = $_POST["starttime"];
                $end_time = $_POST["endtime"];
                $location = $_POST["location"];
                $day = $_POST["day"];
                $geo = geocode($location);
                if ($geo) {
                    $lati = $geo[0];
                    $long = $geo[1];
                }
                $data = array(
                    "event_name" => $event_name,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "location" => $location,
                    "lati" => $lati,
                    "long" => $long,
                    "day" => $day,
                );
                write_data($data);
                header('Location: calendar.php');
            } else {
                if (empty($_POST["eventname"])) {
                    echo "<p class='alert'>Please provide a value for Event Name.</p>";
                }
                if (empty($_POST["starttime"])) {
                    echo "<p class='alert'>Please provide a value for Start Time.</p>";
                }
                if (empty($_POST["endtime"])) {
                    echo "<p class='alert'>Please provide a value for End Time.</p>";
                }
                if (empty($_POST["location"])) {
                    echo "<p class='alert'>Please provide a value for Event Location.</p>";
                }
                if (empty($_POST["day"])) {
                    echo "<p class='alert'>Please provide a value for Event Day.</p>";
                }
            }
        } else if (isset($_POST['clear'])) {
            clear();
            echo "<p class='alert'>Cleared all events.</p>";
            header('Location: calendar.php');
        }
    ?>

    <div class="center">
        <form onsubmit="return valForm()" action="form.php" method="POST">
            <table>
                <tr>
                    <th>Event Name</th>
                    <th><input name="eventname" type="text"></th>
                </tr>

                <tr>
                    <th>Start Time</th>
                    <th><input name="starttime" type="time"></th>
                </tr>

                <tr>
                    <th>End Time</th>
                    <th><input name="endtime" type="time"></th>
                </tr>

                <tr>
                    <th>Location</th>
                    <th><input name="location" type="text"></th>
                </tr>

                <tr>
                    <th>Day of the week</th>
                    <th>
                        <select name="day">
                            <option value="1" selected="selected">Mon</option>
                            <option value="2">Tue</option>
                            <option value="3">Wed</option>
                            <option value="4">Thu</option>
                            <option value="5">Fir</option>
                        </select>
                    </th>
                </tr>

                <tr>
                    <th>
                        <div class="center"><input type="submit" name="clear" value="Clear"></div>
                    </th>
                    <th>
                        <div class="center"><input type="submit" name="submit" value="Submit"></div>
                    </th>
                </tr>
            </table>
        </form>
    </div>

    </body>
</html>

<?php
function geocode($address)
{
    $address = urlencode($address);
    $res = json_decode(
        file_get_contents(
            "http://maps.google.com/maps/api/geocode/json?address={$address}"
        ),
        true
    );
    if ($res['status'] == 'OK') {
        return array(
            $res['results'][0]['geometry']['location']['lat'],
            $res['results'][0]['geometry']['location']['lng'],
            $res['results'][0]['formatted_address']
        );
    }
    return false;
}
?>