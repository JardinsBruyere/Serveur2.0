<!DOCTYPE html>
<html>
      
<head>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
    <link rel="icon" href="favicon_io/favicon.ico">

    <title>
        Gestionnaire serveur
    </title>
</head>
  
<body>
      
    <div class="wrapper">
        <nav id="sidebar">
            <div id="dismiss">
                <i class="fas fa-arrow-left"></i>
            </div>

            <div class="sidebar-header">
                <h3>Bootstrap Sidebar</h3>
            </div>

            <ul class="list-unstyled components">
                <p>Dummy Heading</p>
                <li class="active">
                    <a class="blackColor" href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">Home</a>
                    <ul class="collapse list-unstyled" id="homeSubmenu">
                        <li>
                            <a class="blackColor" href="#">Home 1</a>
                        </li>
                        <li>
                            <a class="blackColor"  href="#">Home 2</a>
                        </li>
                        <li>
                            <a class="blackColor" href="#">Home 3</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="blackColor" href="#">About</a>
                    <a class="blackColor" href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Pages</a>
                    <ul class="collapse list-unstyled" id="pageSubmenu">
                        <li>
                            <a class="blackColor" href="#">Page 1</a>
                        </li>
                        <li>
                            <a class="blackColor" href="#">Page 2</a>
                        </li>
                        <li>
                            <a class="blackColor" href="#">Page 3</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="blackColor" href="/database.php">Database</a>
                </li>
                <li>
                    <a class="blackColor" href="#">Contact</a>
                </li>
            </ul>

            <ul class="list-unstyled CTAs">
                <li>
                    <a class="blackColor" href="https://bootstrapious.com/tutorial/files/sidebar.zip" class="download">Download source</a>
                </li>
                <li>
                    <a class="blackColor" href="https://bootstrapious.com/p/bootstrap-sidebar" class="article">Back to article</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content">

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>Toggle Sidebar</span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
    <h1 style="color:green;  text-align:center;">
        Interface d'administration du serveur
    </h1>
      
    <h3 style="text-align:center;">
        Vous pouvez ici consulter l'état des différents services :
    </h3>
    <br>
    <br>
      
    <div class="card bg-primary text-white">
    <?php
        if(array_key_exists('StateApi', $_POST)) {
            StateApi();
        }
        else if(array_key_exists('StateListen', $_POST)) {
            StateListen();
        }
        function StateApi() {
            $output = shell_exec("sudo systemctl status api | grep Active");
            echo "<h4>Etat de l'API :</h4><p> $output</p>";
        }
        function StateListen() {
            $output = shell_exec("sudo systemctl status listen | grep Active");
            echo "<h4>Etat du listen :</h4><p> $output</p>";
        }
    ?>
    <br>
    <br>
  
    <form method="post">
        <input type="submit" name="StateApi"
                class="button btn btn-primary" value="Etat de l'api" />
          
        <input type="submit" name="StateListen"
                class="btn btn-primary button" value="Etat du listen" />
    </form>
    </div>
    <br>
    <div>
    <h3>
        Etat du serveur :
    </h3>
    <?php
        $url = 'http://localhost:5001/status';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response=="OK") {
            echo "OK";
        }else{
            echo "Fail";
        }
    ?>
    </div>
    <br>
    </div>

    <div>
    <h3>
        Les différents appels à l'api possibles sont :
    </h3>
    <?php
        $url = 'http://localhost:5001/help';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_json = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($response_json, true);
        foreach ($arr as $k=>$v){
            $key=array_keys($v);
            $content=$v[$key[0]];
            echo"<a><a style=\"text-decoration: underline;\">$key[0] :</a> $content</a><br>";
        }
    ?>
    </div>
     <br>
    <div>
    <h3>
        Statistiques :
    </h3>
    <?php
        $output2 = shell_exec("du -sh capteur.db | cut -f1 | sed -e \"s/ //g\"");
        $output = shell_exec("df . | cut -d ' ' -f13");
        echo "<h4>Espace restant (en octet): ${output}";
        echo "<h4>Espace occupé par la base de donnée : $output2";
        
    ?>
    </div>
    <br>
     <div class="overlay"></div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#sidebar").mCustomScrollbar({
                theme: "minimal"
            });

            $('#dismiss, .overlay').on('click', function () {
                $('#sidebar').removeClass('active');
                $('.overlay').removeClass('active');
            });

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').addClass('active');
                $('.overlay').addClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
        });
    </script>
</body>
  
</html>