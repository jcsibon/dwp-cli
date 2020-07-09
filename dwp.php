#! /c/local/php/php.exe
<?php
ini_set('date.timezone','Europe/Paris');

class DwpCli
{
	public $token;
	public $data;
	public $today;
	public $debug;

    public function __construct()
    {
		$this->dirname = dirname(__FILE__);
		$this->token = str_replace("Authorization: bearer ", "", json_decode(file_get_contents($this->dirname.DIRECTORY_SEPARATOR."token.txt"), true)['access_token']);
		$this->today = new DateTime();
		$this->debug = false;

		if($this->debug)
		{
			error_reporting(E_ALL);
		}
    }

    public function runCommand($argv)
    {
    	if(count($argv))
    	{
			$subArgs = array();
	    	foreach($argv as $item)
	    	{
	    		if(substr($item,0,2) == "--")
	    			$subArgs[substr($item,2,strpos($item, "=")-2)] = substr($item,strpos($item, "=") + 1);
	    	}

	    	$query = $argv;

	    	switch ($query[0]) {
	    		case 'token':
	    			file_put_contents($this->dirname.DIRECTORY_SEPARATOR."token.txt", $query[1]);
	    			break;
	    		case 'dashboard':
	    			$this->dashboard();
	    			break;
	    		case 'updateSources':
	    			$this->updateSources();
	    			break;
	    		case 'bulk':
	    			foreach(json_decode(file_get_contents($query[1])) as $subquery)
	    				$this->runCommand($subquery);
	    			break;
	    		case 'user':
	    		case 'solution':
	    		    // $this->data = $this->getAll($query[0]);
	    			switch ($query[1]) {
	    				case 'help':
						echo "usage: wp $query[0] get <id> [--field=<field>] [--fields=<fields>] [--format=<format>]".PHP_EOL;
						echo "   or: wp $query[0] create [--post_author=<post_author>] [--post_date=<post_date>] [--post_date_gmt=<post_date_gmt>] [--post_content=<post_content>] [--post_content_filtered=<post_content_filtered>] [--post_title=<post_title>] [--post_excerpt=<post_excerpt>] [--post_status=<post_status>] [--post_type=<post_type>] [--comment_status=<comment_status>] [--ping_status=<ping_status>] [--post_password=<post_password>] [--post_name=<post_name>] [--from-post=<post_id>] [--to_ping=<to_ping>] [--pinged=<pinged>] [--post_modified=<post_modified>] [--post_modified_gmt=<post_modified_gmt>] [--post_parent=<post_parent>] [--menu_order=<menu_order>] [--post_mime_type=<post_mime_type>] [--guid=<guid>] [--post_category=<post_category>] [--tags_input=<tags_input>] [--tax_input=<tax_input>] [--meta_input=<meta_input>] [<file>] [--<field>=<value>] [--edit] [--porcelain]".PHP_EOL;
						echo "   or: wp $query[0] update <id>... [--post_author=<post_author>] [--post_date=<post_date>] [--post_date_gmt=<post_date_gmt>] [--post_content=<post_content>] [--post_content_filtered=<post_content_filtered>] [--post_title=<post_title>] [--post_excerpt=<post_excerpt>] [--post_status=<post_status>] [--post_type=<post_type>] [--comment_status=<comment_status>] [--ping_status=<ping_status>] [--post_password=<post_password>] [--post_name=<post_name>] [--to_ping=<to_ping>] [--pinged=<pinged>] [--post_modified=<post_modified>] [--post_modified_gmt=<post_modified_gmt>] [--post_parent=<post_parent>] [--menu_order=<menu_order>] [--post_mime_type=<post_mime_type>] [--guid=<guid>] [--post_category=<post_category>] [--tags_input=<tags_input>] [--tax_input=<tax_input>] [--meta_input=<meta_input>] [<file>] --<field>=<value> [--defer-term-counting]".PHP_EOL;
						echo "   or: wp $query[0] delete <id>... [--force] [--defer-term-counting]".PHP_EOL;
						echo "   or: wp $query[0] list [--<field>=<value>] [--field=<field>] [--fields=<fields>] [--format=<format>]".PHP_EOL;
						break;
						case 'list':
							$this->list(["object" => $query[0]]);
							break;
						case 'get':
							print_r($this->get($query[0],$query[2]));
							break;
						case 'getAll':
							print_r($this->getAll($query[0]));
							break;
						case 'create':
							print_r($this->submit($query[0], "", $subArgs));
							break;
						case 'update':
							print_r($this->submit($query[0], $query[2], $subArgs));
							break;
						case 'delete':
							$this->delete($query[0],$query[2]);
							break;
						case 'deleteAll':
							$this->deleteAll($query[0]);
							break;
	    				default:
	    					die("Unknown query for ".$query[1]);
	    					break;
	    			}
	    			break;
	    		default:
	    			die("Unknown query");
	    			break;
	    	}
    	}
    	else
    	{
    		die("No argument");
    	}
    }

	public function parseSolution($object)
	{
		foreach([
		    "id" => "string",
		    "name" => "string",
		    "quickDescr" => "string",
		    "maturityDate" => "string",
		    "endUsers" => "string",
		    "logo" => "string",
		    "whyDescr" => "string",
		    "addedValue" => "string",
		    "benefitHseq" => "string",
		    "benefitPlanning" => "string",
		    "benefitCost" => "string",
		    "benefitEfficiency" => "string",
		    "whatDescr" => "string",
		    "medias" => "string",
		    "mstreamLinks" => "string",
		    "phases" => "string",
		    "prequisites" => "string",
		    "implementationCost" => "string",
		    "implementationTime" => "string",
		    "productOwners" => "string",
		    "sites" => "string",
		    "createdAt" => "string",
		    "updatedAt" => "string",
		    "createdBy" => "string",
		    "updatedBy" => "string",
		    "markedBy" => "string",
		    "feedbacks" => "string",
		    "users" => "string",
		    "isPublished" => "string",
		    "viewBy" => "string",
		    "dsoSolution" => "string",
		    "dsoJustification" => "string",
		    "dsoShortDescription" => "string",
		    "dsoDomain" => "string",
		    "dsoISServices" => "string",
		    "dsoMandatoryFor" => "string",
		    "dsoRecommandedFor" => "string",
		    "dsoMandatoriness" => "string",
		    "isbPOSLocation" => "string",
		    "isbServiceOwner" => "string",
		    "isbFunctionalCoordinator" => "string",
		    "isbITCoordinator" => "string",
		    "isbResponsibleEntity" => "string",
		    "isbInvoicing" => "string",
		    "isbTypeAfe" => "string",
		    "isbCostControlContact" => "string",
		    "isbServiceDescription" => "string",
		    "isbAdded" => "string",
		    "isbPrerequisites" => "string",
		    "isbExcludedElements" => "string",
		    "isbUsageConditions" => "string",
		    "isbOrganisation" => "string",
		    "isbAssistance" => "string",
		    "cmdbBA" => "string",
		    "cmdbBADepartment" => "string",
		    "cmdbKU" => "string",
		    "cmdbKUDepartment" => "string",
		    "cmdbImpact" => "string",
		    "cmdbUsersAffect" => "string",
		    "ed" => "string",
		    "cmdbCountries" => "string",
		    "yammerContact" => "string",
		    "streamContact" => "string",
		    "websiteContact" => "string",
		    "emailContact" => "string",
		    "replacedBy" => "string",
		    "keywords" => "string",
		    "isbSolution" => "string",
		    "isbCode" => "string",
		    "country" => "string",
		    "generalProgram" => "string",
		    "quality" => "string",
		    "commentsForProdig" => "string"
		] as $schemaKey => $schemaType)
		{

		}
		return $object;

		//        "dsoSolution": "string",
		//        "dsoShortDescription": "string",
		//        "dsoDomain": "string",
		//        "dsoMandatoryFor": "string",
		//        "dsoRecommandedFor": "string",
		//        "dsoMandatoriness": "string",
		//        "isbPOSLocation": "string",
		//        "isbTypeAfe": "string",
		//        "isbCostControlContact": "string",
		//        "isbServiceDescription": "string",
		//        "isbAdded": "string",
		//        "isbPrerequisites": "string",
		//        "isbExcludedElements": "string",
		//        "isbUsageConditions": "string",
		//        "isbOrganisation": "string",
		//        "isbAssistance": "string",
		//        "isbSolution": "string",
		//        "isbCode": "string",
		//        "dsoISServices": "stringList",
		//        "dsoJustification": "stringList",
		//        "isbServiceOwner": "userArray",
		//        "isbFunctionalCoordinator": "userArray",        
		//        "isbITCoordinator": "userArray",
		//        "isbResponsibleEntity": "userArray",        
		//        "isbInvoicing": "unknown",


	}

	public function parseUser($object)
	{
		foreach([
		    "id"
		] as $schema)
		{
			echo "TODO";
		}
		return $object;
	}

   public function buildCurl($uri, $post = array())
    {

		$curl = "curl https://qlf.api.apollo.total/prodig-qlf/api/".$uri.PHP_EOL;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://qlf.api.apollo.total/prodig-qlf/api/'.$uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$headers = array();
		$headers[] = 'Connection: keep-alive';
		$headers[] = 'Pragma: no-cache';
		$headers[] = 'Cache-Control: no-cache';
		$headers[] = 'Accept: application/json, text/plain, */*';
		$headers[] = 'Authorization: bearer '.$this->token;
		$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36';
		$headers[] = 'Sec-Fetch-Site: same-site';
		$headers[] = 'Sec-Fetch-Mode: cors';
		$headers[] = 'Sec-Fetch-Dest: empty';
		$headers[] = 'Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7';

		if(count($post))
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_PRETTY_PRINT));
			$headers[] = 'Content-Type: application/json';
		}
		else
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		}

		if($this->debug)
		{
			curl_setopt($ch, CURLOPT_VERBOSE, '1');
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);


		$result = json_decode($result, true);
		// sleep(5);
		
		if(isset($result['fault']['faultstring']))
			die($result['fault']['faultstring'].PHP_EOL);

		// print_r($result);
		return $result;
    }

	public function printList($table)
	{
		foreach($table as $item)
			echo $item["id"]."\t".$item["name"].PHP_EOL;
	}

    public function list($query)
    {
		$this->printList($this->buildCurl($query["object"]));
    }

    public function get($object, $id)
    {
    	$getData = $this->buildCurl($object."/".$id);
    	return $getData;
    }

    public function getAll($object)
    {
    	$getAllData = array_values($this->buildCurl($object));
    	file_put_contents($this->dirname.DIRECTORY_SEPARATOR."history/".$this->today->format('Y-m-d-Hi')."-".$object.".json", json_encode($getAllData,JSON_PRETTY_PRINT));
    	return $getAllData;
    }

    public function submit($object, $id, $parameters)
    {
    	echo "submit".PHP_EOL;
    	foreach($this->data as $key => $item)
    	{
    		if($item['id'] == $id)
    		{
    			foreach($parameters as $paramKey => $paramValue)
    				$this->data[$key][$paramKey] = $paramValue;
    		}
    	}
    	$this->data = array_values($this->data);
    	return $this->buildCurl($object."/addAll", $this->data);
    }

    public function submitAll($object, $data)
    {
    	file_put_contents($this->dirname.DIRECTORY_SEPARATOR."temp.json", json_encode($data,JSON_PRETTY_PRINT));
    	return $this->buildCurl($object."/addAll", $data);
    }

    public function delete($object, $id)
    {
    	$data = $this->getAll($object);
    	foreach($data as $key => $row)
    	{
    		if($row["id"] == $id)
    		{
    			unset($data[$key]);
    			continue;
    		}
    	}
    	$data = array_values($data);
    	echo "Suppression de la base".PHP_EOL;
    	$this->deleteAll($object);
    	echo "Soumission de la base modifiée".PHP_EOL;
    	$this->submitAll($object, $data);
    	print_r($this->list(['object' => $object]));
    }

    public function deleteAll($object)
    {
    	print_r($this->buildCurl($object."/deleteAll"));
    }

    public function updateSources()
    {
		$inputs = array();

    	foreach(["dso","isb"] as $source)
    	{
    		$sources[$source] = array();
			foreach(json_decode(file_get_contents("sources/".$source.".json"),true) as $item)
			{
				$sources[$source][$item["id"]] = $item;
			}
			// print_r($sources);

			foreach(json_decode(file_get_contents("sources/".$source."-dwp.json"),true) as $item)
			{
				foreach($item['sourceItems'] as $sourceItem)
				{
					if(isset($sources[$source][$sourceItem]))
					{
						if(isset($inputs[$item['id']]))
							$inputs[$item['id']] = array_merge($inputs[$item['id']], $sources[$source][$sourceItem]);
						else
							$inputs[$item['id']] = $sources[$source][$sourceItem];

						unset($inputs[$item['id']]['id']);					
					}
				}
			}
    	}
    	// print_r($inputs);


		$newData = $this->buildCurl("solution");

		foreach($newData as $key => $solution)
		{
			if(isset($inputs[$solution['id']]) && count($inputs[$solution['id']]))
			{
				foreach ($inputs[$solution['id']] as $subKey => $value) 
				{
					if(array_key_exists($subKey, $solution))
					{
						$solution[$subKey] = $value;
						// echo $subKey.PHP_EOL;
					}
				}
			}

			if((!isset($solution["whyDescr"]) || !strlen($solution["whyDescr"])) && strlen($solution["isbAdded"]))
			{
				$solution["whyDescr"] = $solution["isbAdded"].PHP_EOL;
			}
			if((!isset($solution["whatDescr"]) || !strlen($solution["whatDescr"])) && strlen($solution["dsoShortDescription"]))
			{
				$solution["whatDescr"] = $solution["dsoShortDescription"].PHP_EOL;
			}

			$newData[$key] = $solution;
		}
		// file_put_contents($this->dirname.DIRECTORY_SEPARATOR."temp.json", json_encode($newData,JSON_PRETTY_PRINT));
		// die();
		// die(json_encode(array_values($newData), JSON_PRETTY_PRINT));
		$this->deleteAll("solution");
		$this->submitAll("solution",$newData);
    }

    public function enrichUser($user)
    {
    	$user['firstLogin'] = new DateTime($user['lastConnections'][0]);
		
		if(is_null($user['lastSolutionsViewed']))
			$user['lastActivity'] = $user['firstLogin'];
		else
			$user['lastActivity'] = new DateTime(end($user['lastSolutionsViewed'])['date']);



	    // echo $user['igg']."\t".$user['firstName']."\t".$user['lastName']."\t".$user['mail']."\t".$user['sigle']."\t".$user['firstLogin']->format("Y-m-d H:i")."\t".$user['lastActivity']->format("Y-m-d H:i").PHP_EOL;
    	return $user;
    }

    public function enrichSolution($solution)
    {
		if(!is_null($solution['viewBy']))
			$solution['totalViews'] = array_sum($solution['viewBy']);
		else
			$solution['totalViews'] = 0;

		$solution['createdAt'] = new DateTime($solution['createdAt']);
		$solution['updatedAt'] = new DateTime($solution['updatedAt']);

		$solution['quality'] = 0;
		(!is_null($solution['logo']) ? $solution['quality'] += 3:'');
		(!is_null($solution['quickDescr']) ? $solution['quality'] += 2:'');
		(!is_null($solution['productOwners']) ? $solution['quality'] += 1:'');

	    $solution['completion'] = 0;
	    $solution['missingFields'] = [];
	    $scoreSheet = [
	        'quickDescr' => "Description courte",
	        'whyDescr' => "Why ?",
	        'whatDescr' => "What ?",
	        'prequisites' => "Prérequis",
	        'productOwners' => "Product Owner",
	//      'benefitHseq'
	//      'benefitPlanning'
	//      'benefitCost'
	//      'benefitEfficiency'
	//      'implementationCost'
	//      'implementationTime'
	    ];
	    
	    $bonusSheet = [
	        'logo',
	        'medias',
	    ];
	    // $solution['upsides']
	        // ['name']
	        // ['maturityDate']
	        // ['endUsers']
	        // ['addedValue']
	        // ['mstreamLinks']
	        // ['phases']
	        // ['productOwners']
	        // ['sites']
	        // ['createdAt']
	        // ['updatedAt']
	        // ['createdBy']
	        // ['updatedBy']
	        // ['markedBy']
	        // ['feedbacks']

	    foreach($scoreSheet as $item => $itemName)
	    {
	        if(isset($solution[$item]) && $solution[$item] != "" && $solution[$item] != array())
	            $solution['completion'] ++;
	        else
	            $solution['missingFields'][] = $itemName;
	    }
	    $solution['completion'] = round($solution['completion'] / count($scoreSheet) * 100, 0)." %";

//	    if($solution['completion'] == "20 %" || $solution['completion'] == "40 %" || $solution['completion'] == "60 %")
//	        echo $solution['completion']."\t".$solution['productOwners'][0]['mail']."\t".$solution['name']." x DW-PRODIG".PHP_EOL;


		return $solution;
    }

    public function formatUser($user)
   	{
	    if(is_null($user))
	        return "<strong><span class=\"text-warning\">[Non identifié]</span></strong>".PHP_EOL;
	    else
	        return "<a href=\"mailto:".$user['mail']."\">".$user['firstName']." ".strtoupper($user['lastName'])."</a><br>(".$user['sigle'].")".PHP_EOL;
   	}

    public function dashboard()
    {
    	$solutions = array();
    	$solutionsCompletionCount = array();

		foreach($this->getAll("solution") as $solution)
		{
			$solution = $this->enrichSolution($solution);

		    if(!isset($solutionsCompletionCount[strval($solution['completion'])]))
		        $solutionsCompletionCount[strval($solution['completion'])] = 1;
		    else
		        $solutionsCompletionCount[strval($solution['completion'])]++;

		    $solutions[] = $solution;
		}

		$users = array();
		$connectedUsersCount = 0;
		$lastSignedInUsersCount = 0;

		foreach($this->getAll("user") as $user)
		{
			if(isset($user['lastConnections']) && $user['lastConnections'])
			{
				$connectedUsersCount++;
				$lastSignedInUsersCount++;
				$users[] = $this->enrichUser($user);
			}
		}

    	$report = "";
		$report.= "<!doctype html>".PHP_EOL;
		$report.= "<html lang=\"en\">".PHP_EOL;
		$report.= "  <head>".PHP_EOL;
		$report.= "    <meta charset=\"utf-8\">".PHP_EOL;
		$report.= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">".PHP_EOL;
		$report.= "    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css\" integrity=\"sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh\" crossorigin=\"anonymous\">".PHP_EOL;
		$report.= "    <title>Statistiques DW-PRODIG au ".$this->today->format('j M Y')."</title>".PHP_EOL;
		$report.= "  </head>".PHP_EOL;
		$report.= "  <body>".PHP_EOL;
		$report.= "  <div class=\"container-fluid\">".PHP_EOL;
		$report.= "  <img class=\"img-fluid\" src=\"https://totalworkplace.sharepoint.com/sites/dw-prodig/Assets/banner.png\">".PHP_EOL;
		$report .= "<h1 class=\"pt-3 pb-1 text-center\">Statistiques DW-PRODIG au ".$this->today->format('j M Y')."</h1>".PHP_EOL;
		$report .= "<p>Nombre de fiches : ".number_format(count($solutions),0,","," ")."</p>".PHP_EOL;
		// $report .= "<p>Nombre de fiches publiées : ".number_format(count($draftSolutions),0,","," ")."</p>".PHP_EOL;
		$report .= "<p>Nombre de fiches - Informations de base 100% : ".$solutionsCompletionCount["100 %"]."</p>".PHP_EOL;
		$report .= "<p>Nombre de fiches - Informations de base 80% : ".$solutionsCompletionCount["80 %"]."</p>".PHP_EOL;
		$report .= "<p>Nombre de fiches - Informations de base 60% : ".$solutionsCompletionCount["60 %"]."</p>".PHP_EOL;
		$report .= "<p>Nombre de fiches - Informations de base 40% : ".$solutionsCompletionCount["40 %"]."</p>".PHP_EOL;
		$report .= "<p>Nombre de fiches - Informations de base 20% : ".$solutionsCompletionCount["20 %"]."</p>".PHP_EOL;
		$report .= "<p>Nombre de fiches - Informations de base 0% : ".$solutionsCompletionCount["0 %"]."</p>".PHP_EOL;
		$report .= "<p></p>".PHP_EOL;
		$report .= "<p>Nombre d'utilisateurs : ".number_format($connectedUsersCount,0,","," ")."</p>".PHP_EOL;
		// $report .= "<p>Nombre d'utilisateurs connectés au cours des ".$days." derniers jours : ".number_format($lastSignedInUsersCount,0,","," ")."</p>".PHP_EOL;
		$report .= "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">".PHP_EOL;
		$report .= "  <li class=\"nav-item\">".PHP_EOL;
		$report .= "    <a class=\"nav-link active\" id=\"solutions-tab\" data-toggle=\"tab\" href=\"#solutionsTab\" role=\"tab\" aria-controls=\"solutionsTab\" aria-selected=\"true\">Solutions</a>".PHP_EOL;
		$report .= "  </li>".PHP_EOL;
		$report .= "  <li class=\"nav-item\">".PHP_EOL;
		$report .= "    <a class=\"nav-link\" id=\"users-tab\" data-toggle=\"tab\" href=\"#usersTab\" role=\"tab\" aria-controls=\"usersTab\" aria-selected=\"false\">Users</a>".PHP_EOL;
		$report .= "  </li>".PHP_EOL;
		$report .= "</ul>".PHP_EOL;
		$report .= "<div class=\"tab-content\">".PHP_EOL;
		$report .= "  <div class=\"tab-pane fade show active\" id=\"solutionsTab\" role=\"tabpanel\" aria-labelledby=\"solutionsTab\">".PHP_EOL;
		$report .= "<br>".PHP_EOL;
		$report .= "<table id=\"solutions\" class=\"table table-bordered table-hover\"  width='100%'>".PHP_EOL;
		$report .= "<thead>".PHP_EOL;
		$report .= "<th width='30%'></th>".PHP_EOL;
		$report .= "<th width='20%' align='center'>Completion</th>".PHP_EOL;
		$report .= "<th width='20%' align='center'>Missing fields</th>".PHP_EOL;
		$report .= "<th width='20%' align='center'>CreatedBy</th>".PHP_EOL;
		$report .= "<th width='20%' align='center'>Product Owner</th>".PHP_EOL;
		$report .= "<th width='10%' align='center'>Statut</th>".PHP_EOL;
		$report .= "<th width='10%' align='center'>UpdatedAt</th>".PHP_EOL;
		$report .= "<th width='10%' align='center'>Actions</th>".PHP_EOL;
		$report .= "</thead>".PHP_EOL;
		$report .= "<tbody>".PHP_EOL;

		foreach($solutions as $solution)
		{ // Rajouter la condition
		    $report .= "<tr>".PHP_EOL;
		    $report .= "<td><a target='_blank' href='https://prodig-qlf.apollo.total/solution/view/".$solution['id']."'>".$solution['name']."</a>".(strlen($solution['commentsForProdig']) ? " <span  data-toggle=\"tooltip\" data-placement=\"top\" title=\"".$solution['commentsForProdig']."\">✉️</span>" : "")."<br>".(strlen($solution['generalProgram']) > 1 ? $solution['generalProgram']." | " : "").$solution['isbCode']."</td>".PHP_EOL;
		    $report .= "<td><strong>".$solution['completion']."</strong></td>".PHP_EOL;
		    $report .= "<td>".implode("<br>", $solution['missingFields'])."</td>".PHP_EOL;
		    $report .= "<td>".$this->formatUser($solution['createdBy'])."</td>".PHP_EOL;
		    $report .= "<td>".(isset($solution['productOwners']) && count($solution['productOwners']) ? $this->formatUser($solution['productOwners'][0]) : "").(isset($solution['productOwners']) && count($solution['productOwners']) > 1 ? "<br><br>".$this->formatUser($solution['productOwners'][1]) : "")."</td>".PHP_EOL;
		    $report .= "<td align='center'>".($solution['isPublished'] ? "Publiée" : "<strong><span class='text-warning'>Brouillon</span></strong>")."</td>".PHP_EOL;
			$report .= "<td align='center'>".$solution['updatedAt']->format("Y-m-d H:i")."</td>".PHP_EOL;
		    $report .= "<td align='center'>".(isset($solution['mail']) ? "<a target='_blank' href='mailto:".$solution['mail']."'>Demander MAJ</a>&nbsp;-&nbsp;" : "")."<a target='_blank' href='https://prodig-qlf.apollo.total/solution/edit/".$solution['id']."'>Editer</a></td>".PHP_EOL;
		//  $report .= "<td>".$solution['id']."</td><td>".$solution['createdAt']."</td><td>".$solution['name']."</td>".PHP_EOL;
		//  $report .= "<td>".$user['firstName']." ".strtoupper($user['lastName'])." (".$user['sigle'].")</td>".PHP_EOL;
		//  $report .= "<td align='center'><a target='_blank' href=\"".htmlspecialchars("mailto:".rawurlencode($user['mail'])."?subject=".rawurlencode('Votre première connexion sur DW-PRODIG')."&body=".rawurlencode("Bonjour,\r\nJe suis Jean-Christophe Sibon et je m'occupe de l'animation de l'outil DW-PRODIG, sur lequel je comprends que vous avez navigué pour la première fois hier.\r\nCet outil a été imaginé pour permettre etc.\r\nA bientôt\r\nJean-Christophe"))."\">Onboarder</a></td>".PHP_EOL;
		    $report .= "</tr>".PHP_EOL;
		}

		$report .= "</tbody>".PHP_EOL;
		$report .= "</table>".PHP_EOL;

		$report .= "  </div>".PHP_EOL;
		$report .= "  <div class=\"tab-pane fade\" id=\"usersTab\" role=\"tabpanel\" aria-labelledby=\"usersTab\">".PHP_EOL;
		$report .= "<br>".PHP_EOL;
		$report .= "<table id=\"users\" class=\"table table-bordered table-hover\"  width='100%'>".PHP_EOL;
		$report .= "<thead>".PHP_EOL;
		$report .= "<th width='70%'></th>".PHP_EOL;
		$report .= "<th width='10%' align='center'>firstLogin</th>".PHP_EOL;
		$report .= "<th width='10%' align='center'>lastActivity</th>".PHP_EOL;
		$report .= "<th width='10%' align='center'><strong>Actions</strong></th>".PHP_EOL;
		$report .= "</thead>".PHP_EOL;
		foreach($users as $user)
		{ // Rajouter la condition
		    $report .= "<tr>".PHP_EOL;
		    $report .= "<td>".$this->formatUser($user)."</td>".PHP_EOL;
		    $report .= "<td align='center'>".$user['firstLogin']->format("Y-m-d H:i")."</td>".PHP_EOL;
		    $report .= "<td align='center'>".$user['lastActivity']->format("Y-m-d H:i")."</td>".PHP_EOL;
		    $report .= "<td align='center'>".PHP_EOL;
		    $report .= "<a target='_blank' href=\"".htmlspecialchars("mailto:".rawurlencode($user['mail'])."?subject=".rawurlencode('Votre première connexion sur DW-PRODIG')."&body=".rawurlencode("Bonjour,\r\nJe suis Jean-Christophe Sibon et je m'occupe de l'animation de l'outil DW-PRODIG, sur lequel je comprends que vous avez navigué pour la première fois hier.\r\nCet outil a été imaginé pour permettre etc.\r\nA bientôt\r\nJean-Christophe"))."\">Demander un feedback</a>".PHP_EOL;
		    $report .= "</td>".PHP_EOL;
		    $report .= "</tr>".PHP_EOL;
		}
		$report .= "</table>".PHP_EOL;
		$report .= "  </div>".PHP_EOL;
		$report .= "</div>".PHP_EOL;
		$report .= "</div>".PHP_EOL;
		$report.= "    <script src=\"https://code.jquery.com/jquery-3.4.1.slim.min.js\" integrity=\"sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n\" crossorigin=\"anonymous\"></script>".PHP_EOL;
		$report.= "    <script src=\"https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js\" integrity=\"sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo\" crossorigin=\"anonymous\"></script>".PHP_EOL;
		$report.= "    <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js\" integrity=\"sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6\" crossorigin=\"anonymous\"></script>".PHP_EOL;
		// $report.= "    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css\">  ".PHP_EOL;
		foreach([
		    "https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css",
		    "https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css",
		//  "https://cdn.datatables.net/colreorder/1.5.2/css/colReorder.dataTables.min.css",    
		] as $css)
		    $report.= "    <link rel=\"stylesheet\" type=\"text/css\" href=\"".$css."\">  ".PHP_EOL;

		foreach([
		    "https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js",
		    "https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js",
		    "https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js",
		    "https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js",
		    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
		    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
		    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
		    "https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js",
		    "https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js",
		//  "https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js",
		] as $script)
		    $report.= "    <script type=\"text/javascript\" charset=\"utf8\" src=\"".$script."\"></script>".PHP_EOL;

		$report.= "    <script>".PHP_EOL;
		$report.= "        $(function () {".PHP_EOL;
		$report.= "          $('[data-toggle=\"tooltip\"]').tooltip()".PHP_EOL;
		$report.= "        })".PHP_EOL;
		$report.= "        $(document).ready( function () {".PHP_EOL;
		$report.= "            $('#solutions, #users').DataTable({".PHP_EOL;
		// $report.= "                colReorder: true,".PHP_EOL;
		$report.= "                dom: 'Bfrtip',".PHP_EOL;
		$report.= "                paging: false,".PHP_EOL;
		$report.= "                buttons: ['csv']".PHP_EOL;
		$report.= "            });".PHP_EOL;
		$report.= "        } );".PHP_EOL;
		$report.= "    </script>".PHP_EOL;
		$report.= "  </body>".PHP_EOL;
		$report.= "</html>".PHP_EOL;

		file_put_contents($this->dirname.DIRECTORY_SEPARATOR."/reports/dwp-".$this->today->format('Y-m-d-Hi').".html", $report);
		exec("chrome ".str_replace("\\", "/", str_replace("C:\\", "/c/", $this->dirname))."/reports/dwp-".$this->today->format('Y-m-d-Hi').".html");
    }
}

$app = new DwpCli();

$app->runCommand(array_slice($argv,1));
