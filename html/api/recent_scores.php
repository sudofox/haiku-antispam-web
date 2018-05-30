<?php

require_once '/home/haiku-watch/config.php';

$db = new SQLite3(DATABASE_PATH);

// Caching -
if (file_exists("./recent_scores_cache.json") && ( time()-filemtime("./recent_scores_cache.json") < 60 ) ) {

	header("Access-Control-Allow-Origin: http://h.hatena.ne.jp");
	header("Content-type: application/json");
	header("Expires: ".gmdate('D, d M Y H:i:s \G\M\T', filemtime("./recent_scores_cache.json") + 60 ) );
	exit(file_get_contents("./recent_scores_cache.json"));
}

//$data["query"]["spam"] = $db->query("SELECT hatena_id, count(*) as count from antispam_unclassified where spam_check_judgement = 1 and timestamp > (strftime('%s', 'now')-86400) group by hatena_id");
//$data["query"]["ham"] = $db->query("SELECT hatena_id, count(*) as count from antispam_unclassified where spam_check_judgement = 0 and timestamp > (strftime('%s', 'now')-86400) group by hatena_id");
$data["query"]["score"] = $db->query("SELECT count(*) count, hatena_id, avg(spam_check_score) average_score, count(case spam_check_judgement when 1 then 1 else null end) marked_spam, count(case spam_check_judgement when 0 then 1 else null end) marked_ham from antispam_unclassified where timestamp > (strftime('%s', 'now')-86400*2) group by hatena_id");
/*
while ($row = $data["query"]["spam"]->fetchArray()) {
	$data["spam"][$row["hatena_id"]] = $row["count"];
}

while ($row = $data["query"]["ham"]->fetchArray()) {
	$data["ham"][$row["hatena_id"]] = $row["count"];

*/
while ($row = $data["query"]["score"]->fetchArray()) {
	$data["avg_score"][$row["hatena_id"]] = round($row["average_score"],1);
}
/*
$data["plot"]["spam"]["json"] = json_encode(array("values"=>$data["plot"]["spam"]["values"], "labels"=>$data["plot"]["spam"]["labels"]));

$data["plot"]["ham"]["json"] = json_encode(array("values"=>$data["plot"]["ham"]["values"], "labels"=>$data["plot"]["ham"]["labels"]));
*/
header("Content-type: application/json");
header("Access-Control-Allow-Origin: http://h.hatena.ne.jp");
unset($data["query"]);

$results = json_encode($data["avg_score"]);
file_put_contents("./recent_scores_cache.json", $results);
exit($results);
