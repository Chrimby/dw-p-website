<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

$ungeeignet_low=0;
$ungeeignet_high=29;
$mittel_low=30;
$mittel_high=59;
$geeignet_low=60;
$geeignet_high=100;
	
function sql_verbindung(){  
    $connection_details=array();
    $connection_details[] = "localhost";
    $connection_details[] = "web3dbqcsum";
    $connection_details[]  = "2r#HupBiM!ansyi3fDts^oba";
    $connection_details[]  ="web3dbqcsum";
    return $connection_details;
}

function daten_gueltig ($keyword){

}


function quickcheck_name(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);			
		if (isset($_GET['admin_access'])){
			$sql= "Select nachname from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			$sql= "Select nachname from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        return $result['nachname'];
    }
    else {
        return "None";
    }
}

function quickcheck_datum(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);		
		if (isset($_GET['admin_access'])){
			$sql= "Select timestamp from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
      
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		return date("d.m.Y - H:i",strtotime($result['timestamp']));

    }
    else {
        return "None";
    }
}

function quickcheck_POST(){
    if (isset($_GET['id'])){
		return $_GET['id'];
      
    }
    else {
        return "NONE";
    }

}

function quickcheck_score(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);	
		if (isset($_GET['admin_access'])){
			 $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			 $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
       
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		$lang = apply_filters( 'wpml_current_language', NULL );
		if ($lang=="en"){
			return $result['score']." % Suitability";
		}
		else {
			return $result['score']." % Eignung";
		}
        
    }
    else {
        return "None";
    }
}

function quickcheck_eignungsbeschreibung(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    
    if (strlen($keyword)==26){
		$lang = apply_filters( 'wpml_current_language', NULL );
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
			
   		if (isset($_GET['admin_access'])){
			 $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			 $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		$score=intval($result['score']);
		if ($lang=="en"){
			if ($score<30){
				return "Malta might not be the right place for you.";
			}
			else if ($score<60 && $score>29){
				return "There is a chance that Malta is the right place for you.";
			}
			
			else {
				return "Malta is the right place for you";
			}
		}
		else {
			if ($score<30){
				return "Malta eignet sich eher nicht für Sie.";
			}
			else if ($score<60 && $score>29){
				return "Malta eignet sich nur unter Umständen für Sie.";
			}
			
			else {
				return "Malta eignet sich für Sie.";
			}
		}

    }
    else {
        return "None";
    }
}

function quickcheck_klassen(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]); 
			
		if (isset($_GET['admin_access'])){
			 $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			 $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		$score=intval($result['score']);
		if ($score<30){
			return "qc_niedrig";
		}
		else if ($score<60 && $score>29){
			return "qc_mittel";
		}
		
		else {
			return "qc_hoch";
		}

    }
    else {
        return "None";
    }
}



function quickcheck_ablauf(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);        	
		if (isset($_GET['admin_access'])){
			  $sql= "Select timestamp from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
       
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        $ablauf=strtotime($result['timestamp'])+7*24*60*60;
        return date("d.m.Y - H:i",$ablauf);
    }
    else {
        return "None";
    }
}

function quickcheck_ablauf_nachricht(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
		$lang = apply_filters( 'wpml_current_language', NULL );
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
       	
       
		if (isset($_GET['admin_access'])){
			  $sql= "Select timestamp from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		$ablauf=strtotime($result['timestamp'])+7*24*60*60;
		if ($lang=="de"){
			return "Achtung! Nur gültig bis: ".date("d.m.Y - H:i",$ablauf);
		}
		else {
			return "Caution! Valid until: ".date("d.m.Y - H:i",$ablauf);
		}
        

    }
    else {
        return "None";
    }
}

function quickcheck_preis_einfach(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
      		
     
		if (isset($_GET['admin_access'])){
			     $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			   $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        if ($result['score']<30){
            return "19";
        }
        else if ($result['score']<60){
            return "19";
        }
        else {
            return "14";
        }

    }
    else {
        return "39";
    }
}

function quickcheck_preis_premium(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
       
		if (isset($_GET['admin_access'])){
			    $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			   $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        if ($result['score']<30){
            return "160";
        }
        else if ($result['score']<60){
            return "119";
        }
        else {
            return "89";
        }

    }
    else {
        return "179";
    }
}
function quickcheck_success(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
		if (isset($_GET['admin_access'])){
		$sql= "Select timestamp from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		if (mysqli_num_rows($stmt)>0){
			return "qc_show";
		}
		else {
			return "qc_hide";
		}
    }
    else {
        return "qc_hide";
    }
}
function quickcheck_fail(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
        
		if (isset($_GET['admin_access'])){
			    $sql= "Select timestamp from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);		

			  $sql= "Select timestamp from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		$time=strtotime($result['timestamp']);
		mysqli_close($conn);
		if (mysqli_num_rows($stmt)>0){
			return "qc_hide";
		}
		else {
			return "qc_show";
		}
       

    }
    else {
        return "qc_show";
    }
}

function quickcheck_anzahl_positive(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
        	
		if (isset($_GET['admin_access'])){
			$sql= "Select PSID from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			  $sql= "Select PSID from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		if (isset($_GET['admin_access'])){
			   $sql= "Select Count(*) AS anzahl from QC_detail Where ID=".$result['PSID']." AND score_kategorie='hoch';";
		}
		else {
			  $sql= "Select Count(*) AS anzahl from QC_detail Where ID=".$result['PSID']." AND score_kategorie='hoch';";
		}
		
		$stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        return $result['anzahl'];

    }
    else {
        return "0";
    }
}
function quickcheck_anzahl_neutrale(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
        	
		if (isset($_GET['admin_access'])){
			$sql= "Select PSID from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			  $sql= "Select PSID from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		if (isset($_GET['admin_access'])){
			   $sql= "Select Count(*) AS anzahl from QC_detail Where ID=".$result['PSID']." AND score_kategorie='mittel';";
		}
		else {
			  $sql= "Select Count(*) AS anzahl from QC_detail Where ID=".$result['PSID']." AND score_kategorie='mittel';";
		}
		
		$stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        return $result['anzahl'];

    }
    else {
        return "0";
    }
}
function quickcheck_anzahl_kritische(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
        return "None";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
        	
		if (isset($_GET['admin_access'])){
			$sql= "Select PSID from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			  $sql= "Select PSID from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
        
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		if (isset($_GET['admin_access'])){
			   $sql= "Select Count(*) AS anzahl from QC_detail Where ID=".$result['PSID']." AND score_kategorie='niedrig';";
		}
		else {
			  $sql= "Select Count(*) AS anzahl from QC_detail Where ID=".$result['PSID']." AND score_kategorie='niedrig';";
		}
		
		$stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
        return $result['anzahl'];

    }
    else {
        return "0";
    }
}


function logged_in_user_check(){
	if (is_user_logged_in()){
		return "community_logged_in";
	}
	else{
		return "community_logged_out";
	}
}

function quickcheck_keyword(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
		return $keyword;
    }
    else {
        $keyword="";
		return "NONE";
    }
}

function quickcheck_detail_check(){
	if (isset($_GET['id'])){
        $keyword=$_GET['id'];
	}
	else if (isset($_GET['id']) && isset($_GET['admin_access'])){
		$keyword=$_GET['id'];
	}
    else {
		return "qc_hide";
	}
       
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();   
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);

     	if (isset($_GET['admin_access'])){
			   $sql= "Select timestamp from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);	
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND detail='Ja' AND timestamp>'".$woche."';";
		}
       
        $stmt=mysqli_query($conn, $sql);
		mysqli_close($conn);
		if (mysqli_num_rows($stmt)!=0) {
			return "qc_show";
		}
		else {
			return "qc_hide";
		}
       

    }
    else {
        return "qc_hide";
    }
}





function quickcheck_positive_details(){
	


	
     if (isset($_GET['id']) || isset($_GET['admin_access'])){
        $verbindung= sql_verbindung();   
		
		$conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
		if (isset($_GET['id'])){
			$keyword=$_GET['id'];
		}
		else if (isset($_GET['id']) && isset($_GET['admin_access']) ){
			$keyword=$_GET['id'];
		}
		else {
			$keyword="";
		}
		          
	
        			
		 if (isset($_GET['admin_access']) && strlen($keyword)==26){
			   $sql= "Select timestamp from QC Where keyword ='".$keyword."';";       
		}
		else if (strlen($keyword)==26){
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND detail='Ja' AND timestamp>'".$woche."';";
			         
		}
		else {
			$sql= "Select PSID from QC Where PSID =9999999;"; 
		}
		
        $stmt=mysqli_query($conn, $sql);
        $success=mysqli_num_rows($stmt);
    }
    else {
        $keyword="";
    }
	


if (strlen($keyword)==26 && $success!=0){
    
    	
			   $sql= "Select * from QC_detail Where ID = (Select PSID from QC Where keyword ='".$keyword."') AND score_kategorie='hoch';";      
		
       
        $stmt=mysqli_query($conn, $sql);
       $anzahl_positive= mysqli_num_rows($stmt);
        $ergebnisse_positiv=array();
        while($row = mysqli_fetch_assoc($stmt)) {
             $ergebnisse_positiv[] = $row;
        }
   



        






        for ($i=0; $i<$anzahl_positive;$i++){
            $frage_ID=  $ergebnisse_positiv[$i]['Frage_ID'];
			$antwort_ID=  $ergebnisse_positiv[$i]['Antwort_ID'];
			$lang = apply_filters( 'wpml_current_language', NULL );
			if ($lang=="en"){
				$sql= "Select PSID, Frage_EN, Antwort_EN, Begruendung_EN, Score from Content Where Frage_ID =".$frage_ID." AND Antwort_ID=".$antwort_ID.";";
			}
			else {
				$sql= "Select PSID, Frage, Antwort, Begruendung, Score from Content Where Frage_ID =".$frage_ID." AND Antwort_ID=".$antwort_ID.";";

			}
            $stmt=mysqli_query($conn, $sql);
            
			$row = mysqli_fetch_assoc($stmt);
			if ($lang=="en"){
				$frage=$row["Frage_EN"];
				$antwort=$row["Antwort_EN"];
				$begruendung=$row["Begruendung_EN"];
				$auswertung_string= "Our valuation:";
				$frage_string = "Question";
				$antwort_string= "Your answer:";
				$bewertung_string = "Score"	;
			}
			else {
				$frage=$row["Frage"];
				$antwort=$row["Antwort"];
				$begruendung=$row["Begruendung"];
				$auswertung_string= "Unsere Einschätzung:";
				$frage_string = "Frage";
				$antwort_string= "Ihre Antwort:";
				$bewertung_string = "Bewertung"	;
			} 
           
     

            $score= $row["Score"]*10;
            
            $ausgabe_neutral.= <<<HEREDOC
<section class="elementor-element elementor-element-fc7eed4 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section" data-id="fc7eed4" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
				<div class="elementor-row">
				<div class="elementor-element elementor-element-8ce23f9 elementor-column elementor-col-75 elementor-inner-column" data-id="8ce23f9" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-85c4ba2 elementor-widget elementor-widget-text-editor is-mac" data-id="85c4ba2" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
					<div class="elementor-text-editor elementor-clearfix"><h4><strong>$frage_string $frage_ID:</strong> $frage </h4><p><strong>$antwort_string</strong> $antwort <br><strong>$auswertung_string</strong> $begruendung</div>
				</div>
				</div>
						</div>
			</div>
		</div>
				<div class="elementor-element elementor-element-1bba125 elementor-column elementor-col-25 elementor-inner-column" data-id="1bba125" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-ae066c6 elementor-widget elementor-widget-counter is-mac" data-id="ae066c6" data-element_type="widget" data-widget_type="counter.default">
				<div class="elementor-widget-container">
					<div class="elementor-counter">
			<div class="elementor-counter-number-wrapper qc_counter">
				<span class="elementor-counter-number-prefix"></span>
				<span class="elementor-counter-number" data-duration="1000" data-to-value="$score" data-from-value="0" data-delimiter=",">$score</span>
				<span class="elementor-counter-number-suffix">%</span>
			</div>
							<div class="elementor-counter-title">$bewertung_string</div>
					</div>
				</div>
				</div>
						</div>
			</div>
		</div>
						</div>
			</div>
		</section>
HEREDOC;

        }    
       
        
    
    mysqli_close($conn);
    return $ausgabe_neutral;
}

else {
 
}

}


function quickcheck_neutral_details(){
    
	
	if (isset($_GET['id']) || isset($_GET['admin_access'])){
        $verbindung= sql_verbindung();   
		
		$conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
		if (isset($_GET['id'])){
			$keyword=$_GET['id'];
		}
		else if (isset($_GET['id']) && isset($_GET['admin_access']) ){
			$keyword=$_GET['id'];
		}
		else {
			$keyword="";
		}
		          
	
        			
		 if (isset($_GET['admin_access']) && strlen($keyword)==26){
			   $sql= "Select timestamp from QC Where keyword ='".$keyword."';";       
		}
		else if (strlen($keyword)==26){
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND detail='Ja' AND timestamp>'".$woche."';";			         
		}
		else {
			$sql= "Select PSID from QC Where PSID =9999999;"; 
		}
		
        $stmt=mysqli_query($conn, $sql);
        $success=mysqli_num_rows($stmt);
    }
    else {
        $keyword="";
    }
	


if (strlen($keyword)==26 && $success!=0){
    
    
     
			   $sql= "Select * from QC_detail Where ID = (Select PSID from QC Where keyword ='".$keyword."') AND score_kategorie='mittel';";      
		
        $stmt=mysqli_query($conn, $sql);
       $anzahl_positive= mysqli_num_rows($stmt);
        $ergebnisse_neutral=array();
        while($row = mysqli_fetch_assoc($stmt)) {
             $ergebnisse_neutral[] = $row;
        }
		
		



        






        for ($i=0; $i<$anzahl_positive;$i++){
            $frage_ID=  $ergebnisse_neutral[$i]['Frage_ID'];
			$antwort_ID=  $ergebnisse_neutral[$i]['Antwort_ID'];
			$lang = apply_filters( 'wpml_current_language', NULL );
			if ($lang=="en"){
				$sql= "Select PSID, Frage_EN, Antwort_EN, Begruendung_EN, Score from Content Where Frage_ID =".$frage_ID." AND Antwort_ID=".$antwort_ID.";";
			}
			else {
				$sql= "Select PSID, Frage, Antwort, Begruendung, Score from Content Where Frage_ID =".$frage_ID." AND Antwort_ID=".$antwort_ID.";";

			}
            $stmt=mysqli_query($conn, $sql);
            
            $row = mysqli_fetch_assoc($stmt);        
      
			if ($lang=="en"){
				$frage=$row["Frage_EN"];
				$antwort=$row["Antwort_EN"];
				$begruendung=$row["Begruendung_EN"];
				$auswertung_string= "Our valuation:";
				$frage_string = "Question";
				$antwort_string= "Your answer:";
				$bewertung_string = "Score"	;
			}
			else {
				$frage=$row["Frage"];
				$antwort=$row["Antwort"];
				$begruendung=$row["Begruendung"];
				$auswertung_string= "Unsere Einschätzung:";
				$frage_string = "Frage";
				$antwort_string= "Ihre Antwort:";
				$bewertung_string = "Bewertung"	;
			} 
     

            $score= $row["Score"]*10;
            
            $ausgabe_neutral.= <<<HEREDOC
<section class="elementor-element elementor-element-fc7eed4 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section" data-id="fc7eed4" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
				<div class="elementor-row">
				<div class="elementor-element elementor-element-8ce23f9 elementor-column elementor-col-75 elementor-inner-column" data-id="8ce23f9" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-85c4ba2 elementor-widget elementor-widget-text-editor is-mac" data-id="85c4ba2" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
				<div class="elementor-text-editor elementor-clearfix"><h4><strong>$frage_string $frage_ID:</strong> $frage </h4><p><strong>$antwort_string</strong> $antwort <br><strong>$auswertung_string</strong> $begruendung</div>
				</div>
				</div>
						</div>
			</div>
		</div>
				<div class="elementor-element elementor-element-1bba125 elementor-column elementor-col-25 elementor-inner-column" data-id="1bba125" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-ae066c6 elementor-widget elementor-widget-counter is-mac" data-id="ae066c6" data-element_type="widget" data-widget_type="counter.default">
				<div class="elementor-widget-container">
					<div class="elementor-counter">
			<div class="elementor-counter-number-wrapper qc_counter">
				<span class="elementor-counter-number-prefix"></span>
				<span class="elementor-counter-number" data-duration="1000" data-to-value="$score" data-from-value="0" data-delimiter=",">$score</span>
				<span class="elementor-counter-number-suffix">%</span>
			</div>
			<div class="elementor-counter-title">$bewertung_string</div>
			</div>
				</div>
				</div>
						</div>
			</div>
		</div>
						</div>
			</div>
		</section>
HEREDOC;

        }    
       
        
    
    mysqli_close($conn);
    return $ausgabe_neutral;
}

else {
 
}

}


function quickcheck_negative_details(){
  
	if (isset($_GET['id']) || isset($_GET['admin_access'])){
        $verbindung= sql_verbindung();   
		
		$conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
		if (isset($_GET['id'])){
			$keyword=$_GET['id'];
		}
		else if (isset($_GET['id']) && isset($_GET['admin_access']) ){
			$keyword=$_GET['id'];
		}
		else {
			$keyword="";
		}
		          
	
        			
		 if (isset($_GET['admin_access']) && strlen($keyword)==26){
			   $sql= "Select timestamp from QC Where keyword ='".$keyword."';";       
		}
		else if (strlen($keyword)==26){
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			$sql= "Select timestamp from QC Where keyword ='".$keyword."' AND detail='Ja' AND timestamp>'".$woche."';";			         
		}
		else {
			$sql= "Select PSID from QC Where PSID =9999999;"; 
		}
		
        $stmt=mysqli_query($conn, $sql);
        $success=mysqli_num_rows($stmt);
    }
    else {
        $keyword="";
    }
	



if (strlen($keyword)==26 && $success!=0){
    
    
   
			  $sql= "Select * from QC_detail Where ID = (Select PSID from QC Where keyword ='".$keyword."') AND score_kategorie='niedrig';";
		
        $stmt=mysqli_query($conn, $sql);
       $anzahl_positive= mysqli_num_rows($stmt);
        $ergebnisse_negativ=array();
        while($row = mysqli_fetch_assoc($stmt)) {
             $ergebnisse_negativ[] = $row;
        }

        for ($i=0; $i<$anzahl_positive;$i++){
            $frage_ID=  $ergebnisse_negativ[$i]['Frage_ID'];
            $antwort_ID=  $ergebnisse_negativ[$i]['Antwort_ID'];            
            
			     
			$lang = apply_filters( 'wpml_current_language', NULL );
			if ($lang=="en"){
				$sql= "Select PSID, Frage_EN, Antwort_EN, Begruendung_EN, Score from Content Where Frage_ID =".$frage_ID." AND Antwort_ID=".$antwort_ID.";";
			}
			else {
				$sql= "Select PSID, Frage, Antwort, Begruendung, Score from Content Where Frage_ID =".$frage_ID." AND Antwort_ID=".$antwort_ID.";";

			}
            $stmt=mysqli_query($conn, $sql);
            
            $row = mysqli_fetch_assoc($stmt);        
      
			if ($lang=="en"){
				$frage=$row["Frage_EN"];
				$antwort=$row["Antwort_EN"];
				$begruendung=$row["Begruendung_EN"];
				$auswertung_string= "Our valuation:";
				$frage_string = "Question";
				$antwort_string= "Your answer:";
				$bewertung_string = "Score"	;
			}
			else {
				$frage=$row["Frage"];
				$antwort=$row["Antwort"];
				$begruendung=$row["Begruendung"];
				$auswertung_string= "Unsere Einschätzung:";
				$frage_string = "Frage";
				$antwort_string= "Ihre Antwort:";
				$bewertung_string = "Bewertung"	;
			}   
         
     

            $score= $row["Score"]*10;
            
            $ausgabe_negativ.= <<<HEREDOC
<section class="elementor-element elementor-element-fc7eed4 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section" data-id="fc7eed4" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
				<div class="elementor-row">
				<div class="elementor-element elementor-element-8ce23f9 elementor-column elementor-col-75 elementor-inner-column" data-id="8ce23f9" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-85c4ba2 elementor-widget elementor-widget-text-editor is-mac" data-id="85c4ba2" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
				<div class="elementor-text-editor elementor-clearfix"><h4><strong>$frage_string $frage_ID:</strong> $frage </h4><p><strong>$antwort_string</strong> $antwort <br><strong>$auswertung_string</strong> $begruendung</div>
				</div>
				</div>
						</div>
			</div>
		</div>
				<div class="elementor-element elementor-element-1bba125 elementor-column elementor-col-25 elementor-inner-column" data-id="1bba125" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-ae066c6 elementor-widget elementor-widget-counter is-mac" data-id="ae066c6" data-element_type="widget" data-widget_type="counter.default">
				<div class="elementor-widget-container">
					<div class="elementor-counter">
			<div class="elementor-counter-number-wrapper qc_counter">
				<span class="elementor-counter-number-prefix"></span>
				<span class="elementor-counter-number" data-duration="1000" data-to-value="$score" data-from-value="0" data-delimiter=",">$score</span>
				<span class="elementor-counter-number-suffix">%</span>
			</div>
			<div class="elementor-counter-title">$bewertung_string</div>
					</div>
				</div>
				</div>
						</div>
			</div>
		</div>
						</div>
			</div>
		</section>
HEREDOC;

        }    
       
        
    
    mysqli_close($conn);
    return $ausgabe_negativ;
}

else {
 
}

}



function quickcheck_einfach_coupon(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
    }
    else {
        $keyword="";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();  		
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
				
		 if (isset($_GET['admin_access'])){
			   
			  $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			  $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
       
        $stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		if ($result['score']<$ungeeignet_high){
			return "EINFACHX1";
		}
		else if ($result['score']<$mittel_high){
			return "EINFACH_TG";
		}
		else{
			return "EINFACH_G";
		}

    }
    else {
        return "";
    }
}

function quickcheck_premium_coupon(){
    if (isset($_GET['id'])){
        $keyword=$_GET['id'];
    }
    else {
        $keyword="";
    }
    if (strlen($keyword)==26){
        $verbindung= sql_verbindung();  		
        $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
	
		 if (isset($_GET['admin_access'])){
			  $sql= "Select score from QC Where keyword ='".$keyword."';";
		}
		else {
			$woche=date("Y-m-d H:i:s", time()-7*24*60*60);
			  
			 $sql= "Select score from QC Where keyword ='".$keyword."' AND timestamp>'".$woche."';";
		}
     
		$stmt=mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($stmt);
		mysqli_close($conn);
		if ($result['score']<$ungeeignet_high){
			return "PremiumX1";
		}
		else if ($result['score']<$mittel_high){
			return "Premium_TG";
		}
		else{
			return "Premium_G";
		}

    }
    else {
        return "";
    }
}


function quickcheck_admin_info(){
if (isset($_GET['admin_access']) && isset($_GET['id'])){
    $keyword=$_GET['id'];
}
else {
    $keyword="";
}
if (strlen($keyword)==26){
    $verbindung= sql_verbindung();   
    $conn = mysqli_connect($verbindung[0], $verbindung[1], $verbindung[2], $verbindung[3]);
    $sql= "Select vorname, nachname, email, telefon, beschreibung,branche, sonst, perspektivischer_umsatz, perspektivischer_gewinn from QC Where keyword ='".$keyword."';";
    $stmt=mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($stmt);
    mysqli_close($conn);
	$vorname=$result['vorname'];
    $nachname=$result['nachname'];
	$branche=$result['branche'];
  $email=$result['email'];
   $telefon=$result['telefon'];
    $beschreibung=$result['beschreibung'];
   $sonst= $result['sonst'];
   if ($result['perspektivischer_umsatz']==1){
	$persumsatz="<100.000€";
   }
   else if ($result['perspektivischer_umsatz']==2){
	$persumsatz="100.000€ - 300.000€";
   }
   else if ($result['perspektivischer_umsatz']==3){
	$persumsatz="300.000€-900.000€";
   }
   else{
	$persumsatz="> 900.000€";
   }
   if ($result['perspektivischer_gewinn']==1){
	$persgewinn="<100.000€";
   }
   else if ($result['perspektivischer_gewinn']==2){
	$persgewinn="100.000€ - 300.000€";
   }
   else if ($result['perspektivischer_gewinn']==3){
	$persgewinn="300.000€-900.000€";
   }
   else{
	$persgewinn="> 900.000€";
   }
  
$response= <<<HEREDOC
    <h2>Admin Infos</h2>
    <p><strong>Name: </strong>$vorname $nachname</p>
	<p><strong>E-Mail: </strong>$email</p>
	<p><strong>Telefon: </strong>$telefon</p>
	<p><strong>Beschreibung: </strong>$beschreibung</p>
	<p><strong>Sonstige Anmerkungen: </strong>$sonst</p>
	<p><strong>Branche:</strong> $branche </p>
	<p><strong>Perspektivischer Umsatz: </strong>$persumsatz</p>
	<p><strong>Perspektivischer Gewinn: </strong>$persgewinn</p>
HEREDOC;
    return $response;
    
}
else {
    return "";
}
}



add_action( 'woocommerce_thankyou', 'thankyouredirect');
  
function thankyouredirect( $order_id ){
    $order = wc_get_order( $order_id );
	$email= $order->get_billing_email();
	$items = $order->get_items();
	foreach ( $items as $item ) {		
		$product_id = $item->get_product_id();
	}
	
	$url = '/qc/order-complete.php?product_id='.$product_id.'&mail='.$email;
	
    if ( ! $order->has_status( 'failed' )) {
        wp_safe_redirect( $url );
        exit;
    }
}







add_shortcode('QC-Name', 'quickcheck_name'); 
add_shortcode('QC-Datum', 'quickcheck_datum'); 
add_shortcode('QC-Score', 'quickcheck_score'); 
add_shortcode('QC-Ablauf', 'quickcheck_ablauf');
add_shortcode('QC-Ablauf-Nachricht', 'quickcheck_ablauf_nachricht'); 
add_shortcode('QC-Preis-Einfach', 'quickcheck_preis_einfach'); 
add_shortcode('QC-Preis-Premium', 'quickcheck_preis_premium'); 
add_shortcode('QC-Success', 'quickcheck_success'); 
add_shortcode('QC-Fail', 'quickcheck_fail');
add_shortcode('QC_POST', 'quickcheck_POST');
add_shortcode('QC_Eignungsclass', 'quickcheck_klassen');
add_shortcode('QC_Eignungsbeschreibung', 'quickcheck_eignungsbeschreibung');
add_shortcode('QC_Anzahl_Positive', 'quickcheck_anzahl_positive');
add_shortcode('QC_Anzahl_Neutrale', 'quickcheck_anzahl_neutrale');
add_shortcode('QC_Anzahl_Kritische', 'quickcheck_anzahl_kritische');
add_shortcode('Check_User', 'logged_in_user_check');
add_shortcode('QC_Keyword', 'quickcheck_keyword');
add_shortcode('QC_Positive_Details', 'quickcheck_positive_details');
add_shortcode('QC_Neutral_Details', 'quickcheck_neutral_details');
add_shortcode('QC_Negative_Details', 'quickcheck_negative_details');
add_shortcode('QC_Detail_Check', 'quickcheck_detail_check');
add_shortcode('QC_Admin_Info', 'quickcheck_admin_info');
add_shortcode('QC_Einfach_Coupon', 'quickcheck_einfach_coupon');
add_shortcode('QC_Premium_Coupon', 'quickcheck_premium_coupon');



