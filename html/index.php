<?php

require_once '/home/haiku-watch/config.php';

$db = new SQLite3(DATABASE_PATH);


$data["query"]["spam"] = $db->query("SELECT hatena_id, count(*) as count from antispam_unclassified where spam_check_judgement = 1 and timestamp > (strftime('%s', 'now')-86400) group by hatena_id");
$data["query"]["ham"] = $db->query("SELECT hatena_id, count(*) as count from antispam_unclassified where spam_check_judgement = 0 and timestamp > (strftime('%s', 'now')-86400) group by hatena_id");

$data["plot"]["spam"]["labels"] = array();
$data["plot"]["spam"]["values"] = array();

$data["plot"]["ham"]["labels"] = array();
$data["plot"]["ham"]["values"] = array();

while ($row = $data["query"]["spam"]->fetchArray()) {
	$data["spam"][$row["hatena_id"]] = $row["count"];

	$data["plot"]["spam"]["labels"][] = $row["hatena_id"];
	$data["plot"]["spam"]["values"][] = $row["count"];
}

while ($row = $data["query"]["ham"]->fetchArray()) {
	$data["ham"][$row["hatena_id"]] = $row["count"];

	$data["plot"]["ham"]["labels"][] = $row["hatena_id"];
	$data["plot"]["ham"]["values"][] = $row["count"];

}

$data["plot"]["spam"]["json"] = json_encode(array("values"=>$data["plot"]["spam"]["values"], "labels"=>$data["plot"]["spam"]["labels"]));

$data["plot"]["ham"]["json"] = json_encode(array("values"=>$data["plot"]["ham"]["values"], "labels"=>$data["plot"]["ham"]["labels"]));

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hatena Haiku Anti-Spam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://lightni.ng/css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
      WebFont.load({
        google: {
          families: ['Crete Round']
        }
      });
    </script>
  <!-- Plotly.js -->
  <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
  <!-- Numeric JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/numeric/1.2.6/numeric.min.js"></script>
  <style>
  .modebar {
	display: none !important;
  }
.nav {
        padding: 10px 20px !important;
}

  </style>

</head>
<body>
  <body>
    <header class="header">
      <nav class="nav">
        <div class="navGroup navGroup--left">
          <a class="nav__item logo" href="http://haikuantispam.lightni.ng">
            <svg class="foxySVG" width="178px" height="169px" viewBox="0 0 178 169">
              <path class="fill--outline" d="M68.0717968,25.660254 C73.0435464,17.048931 84.0510372,14.1012631 92.6660445,19.0751399 L111.076393,29.7043595 C111.472756,29.4786631 146.379518,11.6846411 148.379489,10.9459307 C156.920759,7.79112433 163.802404,7.85285923 170.129219,12.5152998 C175.342085,16.3568342 177.382644,21.7083899 177.378498,28.5473209 C177.376764,31.4078731 176.9935,34.6041556 176.247659,38.4019376 C175.339763,43.0248965 174.206921,47.3566033 171.890483,55.2324949 C170.87731,58.6772805 158.722075,89.0619026 158.722075,89.0619026 C158.906984,91.028659 159.499321,105 157.240286,109.818163 C160.970252,110.900725 163.477144,114.589833 162.924969,118.593099 C162.88918,118.85257 162.828475,119.225261 162.738224,119.696783 C162.599844,120.419766 162.422707,121.209475 162.201346,122.052128 C161.567706,124.464195 160.690323,126.884507 159.499321,129.209659 C158.257836,131.633366 156.760245,133.796875 154.992438,135.612693 C155.062804,137.029764 154.756676,138.486046 154.023165,139.830817 C153.90628,140.045106 149.800675,145.589406 148.137688,147.11381 C141.209796,153.464379 132.243944,156.221989 122.076875,153.315844 C112.139751,158.331146 101.016334,161 89.5,161 C77.8400454,161 66.584162,158.263982 56.556762,153.129655 C46.1495951,156.333953 36.9494558,153.610359 29.8623116,147.11381 C28.1993252,145.589406 24.0937202,140.045106 23.9768354,139.830817 C23.2433241,138.486046 22.9371964,137.029764 23.0075621,135.612693 C20.1228036,134 15.6500619,122.186198 15.075031,118.593099 C14.5,115 17.4649846,110.293126 21.7084835,109.604469 C20.1228036,105 20.0410371,92.3598762 20.1228036,91.0473669 C20.1228036,91.0473669 7.12268973,58.6772805 6.10951748,55.2324949 C3.79307878,47.3566033 2.6602368,43.0248965 1.75234101,38.4019376 C1.00650044,34.6041556 0.623236255,31.4078731 0.621501989,28.5473209 C0.617355751,21.7083899 2.65791461,16.3568342 7.87078081,12.5152998 C14.1975961,7.85285923 21.0792409,7.79112433 29.6205105,10.9459307 C31.6204825,11.6846411 66.1978362,29.2917003 66.4357357,29.426865 C66.8302133,28.1305955 67.3752619,26.8666877 68.0717968,25.660254 Z"></path>
              <path class="fill-black"  d="M151.151338,18.4503865 C156.919499,16.3198565 161.348576,15.9821837 165.383246,18.9554662 C168.494585,21.2483148 169.380972,24.4638671 169.378499,28.5424707 C169.377079,30.8861117 146.802715,96.6410497 146.802715,96.6410497 L104.535515,42.750706 C104.535515,42.750706 149.343958,19.1179611 151.151338,18.4503865 Z"></path>
              <path class="fill--white" d="M115,44.7502385 C115,44.7502385 130,36 140.5,31 C151,26 157.961041,22.1812235 161.23052,24.5906117 C164.5,27 160,42.5 157.5,51 C155,59.5 145,83 145,83 L115,44.7502385 Z"></path>
              <path class="fill-black"  d="M26.8486621,18.4503865 C21.0805013,16.3198565 16.6514236,15.9821837 12.6167535,18.9554662 C9.50541521,21.2483148 8.61902778,24.4638671 8.62150052,28.5424707 C8.6229214,30.8861117 31.197285,96.6410497 31.197285,96.6410497 L73.4644849,42.750706 C73.4644849,42.750706 28.6560423,19.1179611 26.8486621,18.4503865 Z"></path>
              <path class="fill--white" d="M63,44.7502385 C63,44.7502385 48,36 37.5,31 C27,26 20.0389593,22.1812235 16.7694797,24.5906117 C13.5,27 18,42.5 20.5,51 C23,59.5 33,83 33,83 L63,44.7502385 Z"></path>
              <ellipse class="fill--red" cx="89.5" cy="95" rx="61.5" ry="58"></ellipse>
              <path class="fill--white" d="M155,117.5 C155,117.5 153,132 142.5,133.5 C144.3294,134.806714 147,136 147,136 C147,136 138.459394,151.657778 121.378181,144.609909 C112.082135,149.934173 101.170704,153 89.5,153 C77.6514509,153 66.5854916,149.840023 57.1979709,144.364935 C39.732657,152.009871 31,136 31,136 C31,136 33.6705997,134.806714 35.5,133.5 C25,132 23,117.5 23,117.5 L89,112 L155,117.5 Z"></path>
              <path class="fill--black" d="M84.4905829,119.38068 C86.9817991,122.13306 91.0181092,122.135642 93.5111529,119.38068 L97.9761586,114.446584 C100.198465,111.990803 98.3959033,109.350657 95.1423982,108.699503 C95.1423982,108.699503 93,108 89.001329,108 C85.0026579,108 82.8593301,108.699503 82.8593301,108.699503 C79.6233114,109.417748 77.8077791,111.997295 80.0246642,114.446584 L84.4905829,119.38068 Z"></path>
              <path class="fill--grey" d="M74.2442089,129.957704 C74.4010059,130.245166 76.5920037,132.693439 77.6169424,133.398084 C80.4530199,135.347887 84.223571,136.5 89,136.5 C93.776429,136.5 97.5469801,135.347887 100.383058,133.398084 C101.407996,132.693439 103.535718,130.240911 103.755791,129.957704 C104.5,129 103.927401,127.773134 102.957704,127.244209 C101.988008,126.715283 100.773134,127.072599 100.244209,128.042296 C100.213506,128.098584 98.8420037,129.603436 98.1169424,130.101916 C95.9530199,131.589613 92.973571,132.5 89,132.5 C85.026429,132.5 82.0469801,131.589613 79.8830576,130.101916 C79.1579963,129.603436 77.7864941,128.098584 77.7557911,128.042296 C77.2268657,127.072599 76.0119924,126.715283 75.0422957,127.244209 C74.0725991,127.773134 73.7152834,128.988008 74.2442089,129.957704 Z"></path>
              <g class="eyes" transform="translate(51.000000, 89.000000)" fill="#222222">
                  <rect class="fill--black eye" x="66" y="0" width="10" height="14" rx="5"></rect>
                  <rect class="fill--black eye" x="0" y="0" width="10" height="14" rx="5"></rect>
              </g>
              <path class="fill--darkred" d="M75,38.705573 C70.3375166,40.3667711 67,44.8461855 67,50.1139816 C67,54.48284 69.3184762,58.4030485 72.9033467,60.5355578 C71.1581521,64.0921613 71.2199667,68.3744941 73.2679492,71.9505836 C76.5822236,77.7378118 83.9232631,79.7178687 89.6660445,76.3752783 L108.16235,65.6094891 L107.329759,63.7658958 L88.6660445,74.6291259 C83.8799203,77.4148933 77.7620309,75.7653689 75,70.9424421 C72.8953553,67.2674117 73.3455491,62.8124461 75.799742,59.6666688 C71.8461321,58.3202284 69,54.5527398 69,50.1139816 C69,45.9806405 71.4660993,42.4282776 75,40.8721986 L75,38.705573 Z"></path>
              <path class="fill--red" d="M107.707022,63.3238512 L88.6660445,74.317165 C83.8799203,77.0804351 77.7620309,75.4442319 75,70.660254 C72.8953553,67.0149024 73.3455491,62.5959143 75.799742,59.4755416 C71.8461321,58.1399748 69,54.4029116 69,50 C69,45.6689672 71.7519087,41.9810097 75.6074314,40.5909977 C73.3254206,37.4886646 72.951666,33.2080726 75,29.660254 C77.7614237,24.8773278 83.8753221,23.2374183 88.6660445,26.0033431 L107.707022,36.9966569 C112.327925,39.6645363 112.332337,60.6534247 107.707022,63.3238512 Z"></path>
            </svg>
          </a>
          <h1 class="nav__item title"><a href="/" style="color:black;">Hatena Haiku Anti-Spam</a></h1>
        </div>
        <ul class="navGroup navGroup--right nav__links">
          <li class="nav__item"><a href="https://github.com/sudofox/hatena-haiku-spam-filter">GitHub Project</a></li>|
          <li class="nav__item"><a href="http://profile.hatena.ne.jp/austinburk/">id:austinburk on Hatena</a></li>|
          <li class="nav__item"><a href="/id/enter_id_in_url">Lookup by Hatena ID</a></li> 
        </ul>
      </nav>
    </header>
  <div id="mainStats"><!-- Plotly chart will be drawn inside this DIV --></div>
  <script>
var mainStats = document.getElementById("mainStats");
var data = <?php echo json_encode(array(array(
		"values"	=>	$data["plot"]["spam"]["values"],
		"labels"	=>	$data["plot"]["spam"]["labels"],
		"name"		=>	"Spam",
		"text"		=>	"Spam",
		"hoverinfo"	=>	"label+percent+value+name",
		"domain"	=>	array(x=>array(0, .5 )),
		"hole"		=>	.4,
		"type"		=>	"pie"
		),

	array(
                "values"        =>	$data["plot"]["ham"]["values"],
                "labels"        =>	$data["plot"]["ham"]["labels"],
		"text"		=>	"Real",
                "name"          =>	"Real",
                "hoverinfo"     =>	"label+percent+value+name",
		"domain"	=>	array(x=>array(0.5, 1)),
                "hole"          =>	.4,
                "type"          =>	"pie"
		)
	)
);

/*
  labels: ['US', 'China', 'European Union', 'Russian Federation', 'Brazil', 'India', 'Rest of World' ],

  domain: {
    x: [0, .48]
  },
  name: 'GHG Emissions',
  hoverinfo: 'label+percent+name',
  hole: .4,
  type: 'pie'
},{
  values: [27, 11, 25, 8, 1, 3, 25],
  labels: ['US', 'China', 'European Union', 'Russian Federation', 'Brazil', 'India', 'Rest of World' ],
  text: 'CO2',
  textposition: 'inside',
  domain: {x: [.52, 1]},
  name: 'CO2 Emissions',
  hoverinfo: 'label+percent+name',
  hole: .4,
  type: 'pie'
}

*/ ?>;

var layout = {
  title: '',
  annotations: [
    {
      font: {
        size: 20
      },
      showarrow: false,
      text: 'Spam',
      x: 0,
      y: 0
    },
    {
      font: {
        size: 20
      },
      showarrow: false,
      text: 'Real',
      x: .5,
      y: 0
    },
    {
      font: {
        size: 20
      },
      showarrow: false,
      text: 'Hatena IDs',
      x: 1.1,
      y: 1.2
    }

  ],
  height: 600,
  plot_bgcolor: "#efefef",
  paper_bgcolor: "#efefef",
};

Plotly.newPlot('mainStats', data, layout);
mainStats.on('plotly_click', function(data) {
	console.log(data);
	console.log("Clicked on " +data.points[0].label);
	window.open("/id/"+data.points[0].label,'_blank');
});
</script>


    <section class="content wrap">
      <article class="post">
        <div class="post__head">
          <h2 class="post__title"><a href="">Details</a></h2>
        </div>
	<div class="post__body">
	 <p>The charts above show recorded Haiku entries, and their automatic classifications, from the past 24 hours.</p>
	 <p>Click on one of the pie slices to open the user's classification details in a new tab!</p>
	 <p>Please note that this is a work in progress! I will update this soon :)</p>
	 <p>I enjoy Hatena's services and decided to make something to help out a bit.</p>
	</div>
      </article>
    </section>
  <script src="https://lightni.ng/js/main.min.js" charset="utf-8"></script>
  </body>
</html>

