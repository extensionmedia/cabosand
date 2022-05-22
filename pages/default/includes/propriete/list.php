<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } 

$core = $_SESSION["CORE"];
$table_name = "Propriete";
require_once($core.$table_name.".php");  
$ob = new $table_name();

$months = [
	1	=>	'Janvier',
	2	=>	'Février',
	3	=>	'Mars',
	4	=>	'Avril',
	5	=>	'Mai',
	6	=>	'Juin',
	7	=>	'Juillet',
	8	=>	'Août',
	9	=>	'Septembre',
	10	=>	'Octobre',
	11	=>	'Novembre',
	12	=>	'Décembre'
];

$years = [
	2019	=>	'2019',
	2020	=>	'2020',
	2021	=>	'2021',
	2022	=>	'2022'
];

$tags = [
	[ 'hide'=>'', 'id'=>'code', 'label' => 'Code'],
	[ 'hide'=>'hide', 'id'=>'nbr_chambre', 'label' => 'Chambres'],
	[ 'hide'=>'hide', 'id'=>'notes', 'label' => 'Notes'],
	[ 'hide'=>'hide', 'id'=>'proprietaire', 'label' => 'Proprietaire'],
	[ 'hide'=>'hide', 'id'=>'phone', 'label' => 'Téléphone']
];

$filters = [
	'Client'				=>	$ob->find('', ['conditions'=>['id_status='=>11], 'order'=>'societe_name'], 'client'),
	'Type'					=>	$ob->find('', ['order'=>'propriete_type'], 'propriete_type'),
	'Complexe'				=>	$ob->find('', ['order'=>'name'], 'complexe'),
	'Status'				=>	$ob->find('', ['order'=>'propriete_status' ], 'propriete_status'),
	'Années'				=>	$years
];
	
?>

<div id="page" class="">
	<div class="page-head">
		<div class="title d-flex space-between">
			<div class="name">	Appartement</div> 
			<div class="actions d-flex">
				<button class="green add" data-controler="<?= $table_name ?>"><i class="fas fa-plus"></i> Ajouter</button>
			</div>
			
		</div>
		<div class="search d-flex space-between">
			<div class="request d-flex">
				<input type="text" placeholder="chercher" class="mr-5">
				<button class="mr-5 page_search_button" data-controler="<?= $table_name ?>" data-use="v_propriete" data-column_style="v_propriete"><i class="fa fa-search"></i></button>
				
				<!-- TAGS -->
				<div class="tags">
					<ul class="">
						<?php
							foreach($tags as $k=>$v){
								echo '<li class="'.$v["hide"].'" id="'.$v["id"].'">'.$v["label"].'</li>';
							}
						?>
						<li class="show_filters"><i class="fas fa-ellipsis-h"></i></li>
					</ul>
				</div>
				
			</div>
			<div class="filter">
				<?php
					$string = "";
					foreach($filters as $key=>$value){
						$string .= '<select id="'.$key.'">';
						$string .= '	<option value="-1"> -- '.$key." -- </option>";
						foreach($value as $k=>$v){
							if($key === "Type")
								$string .= '<option value="'.$v["id"].'">'. strtoupper( $v["propriete_type"] ) ."</option>";
							if($key === "Complexe")
								$string .= '<option value="'.$v["id"].'">'. strtoupper( $v["name"] ) ."</option>";
							if($key === "Status")
								$string .= '<option value="'.$v["id"].'">'. strtoupper( $v["propriete_status"] ) ."</option>";
							if($key === "Client")
								$string .= '<option value="'.$v["id"].'">'. ucfirst( $v["societe_name"] ) ."</option>";
							if($key === "Années"){
								if( $k == intval(date("Y")) ) 
									$string .= '<option selected value="'.$k.'">'.$v."</option>";
								else
									$string .= '<option value="'.$k.'">'.$v."</option>";
							}
								
						}
						$string .= '</select>';
					}
				echo $string;
				?>
			</div>
		</div>
		<div class="result d-flex space-between">
			
			<div class="totals" style="padding-top: 0!important">
				<button><i class="fas fa-chart-bar"></i></button>
				<button><i class="far fa-file-pdf"></i></button>
				<button class="exportTo" data-target="tablepropriete" data-type="csv"><i class="fas fa-file-csv"></i></button>
				<button><i class="fas fa-at"></i></button>
			</div>
			
			<div class="d-flex nex_prev">
				<div class="pp">
					<select>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="200">200</option>
						<option value="500">500</option>
						<option value="1000">1000</option>
					</select>				
				</div>
				<div class="current hide">0</div>
				<div class="direction">
					<button data-step="-1"><i class="fas fa-angle-left"></i></button>
					<button data-step="1"><i class="fas fa-angle-right"></i></button>					
				</div>

			</div>
		</div>
	</div>
	<div class="page-body" style="padding-left: 10px;">

		<div class="table-container">
			<?php
				$params = [
					'column_style'	=>	'v_propriete',
					'use'			=>	'v_propriete',
					'filters'		=>	[ ['id'=>'Années', 'value'=>2020] ],
					'pp'			=>	20,
					'current'		=>	0

				];
				// echo $ob->Table($params);
			?>
		</div>
	</div>
	<div class="hidden right-container_2 fixed top-0 right-0 left-0 bottom-0 bg-white z-100 mt-12 ml-96 border-gray-500 border-l-2 shadow pb-12">
		<div class="w-full relative">
			<div class="absolute top-4 -left-12 hover:bg-red-500 hover:text-white rounded py-2 px-3 cursor-pointer close_right-container_2 text-red-600">
				<i class="fas fa-times"></i>
			</div>

			<!-- Container Title -->
			<div class="text-lg px-4 border-b flex items-center justify-between">
				<div class="h-12 flex items-center">
					Appartement [<span class="code_here text-xl font-bold"></span>] 
				<span class="right-container_2_ID hidden"></span>
				</div>
				<div class="flex items-center h-12 text-sm pt-2">
					<div data-tab="form" class="show-tab border h-full cursor-pointer bg-green-600 text-white flex mr-1 shadow items-center px-4 hover:bg-gray-300 rounded-t-lg">
						Détails
					</div>
					<div data-tab="depense" class="show-tab border h-full cursor-pointer flex mr-1 shadow items-center px-4 hover:bg-gray-300 rounded-t-lg">
						Dépense
					</div>
					<div data-tab="contrat" class="show-tab border h-full cursor-pointer flex mr-1 shadow items-center px-4 hover:bg-gray-300 rounded-t-lg">
						Propriétaire
					</div>
					<div data-tab="location" class="show-tab border h-full cursor-pointer flex mr-1 shadow items-center px-4 hover:bg-gray-300 rounded-t-lg">
						Client
					</div>
				</div>

				
			</div>

			<div class="tab-container h-full overflow-y-auto px-4 py-6"></div>

			<!-- Container Tabs -->
		</div>
	</div>
	<script>
		$(document).ready(function(){
			$('.page_search_button').trigger('click');

			$(document).on('click', '.tr-highlight', function(){
				$('tr').removeClass('border-l-8 border-red-500')
				$(this).addClass('border-l-8 border-red-500')
			})

			$('.close_right-container_2').on('click', function(){
				$('.right-container_2').toggleClass('hidden')
			})

			$(document).on('click', '.show_right-container_2', function(){
				$('.right-container_2_ID').html($(this).data('id'))
				$('.code_here').html($(this).html())
				$('.right-container_2').removeClass('hidden')
				$('.show-tab.bg-green-600').trigger('click')
			})
			
			$('.show-tab').on('click', function(){
				$('.show-tab').removeClass('bg-green-600 text-white')
				$(this).addClass('bg-green-600 text-white')
				var tab = $(this).data('tab');

				var ID = $('.right-container_2_ID').html();

				$('.tab-container').html('Loading');
				if(tab == 'form'){
					$('.tab-container').html('Loading');
					loadPropriete(ID)
				}else if(tab == 'depense'){
					$('.tab-container').html('Loading');
					loadDepense(ID)
				}else if(tab == 'contrat'){
					$('.tab-container').html('Loading');
					loadContrat(ID)
				}else if(tab == 'location'){
					$('.tab-container').html('Loading');
					loadLocation(ID)
				}else{
					$('.tab-container').html('');
				}
			})

			/** Supprimer periode from location */
			$(document).on('click', '.supprimer_location', function(){
				var id_location = $(this).data('id_location');

				var _next = confirm('Etes vous sure de vouloir supprimer?');
				if(_next){
					var data = {
						'controler'		:	'Propriete_Location',
						'function'		:	'Remove',
						'params'		:	{
							'id'	:	id_location
						}
					};
					
					$.ajax({
						type		: 	"POST",
						url			: 	"pages/default/ajax/ajax.php",
						data		:	data,
						dataType	: 	"json",
					}).done(function(response){
						$('.show-tab.bg-green-600').trigger('click')
					}).fail(function(xhr) {
						console.log(xhr.responseText);
					});					
				}

			})
			
		});

		// Load Propriete Form

		function loadPropriete(id){
			var data = {
				'controler'		:	'Propriete',
				'function'		:	'Edit',
				'params'		:	{
					'id'	:	id
				}
			};
			
			$.ajax({
				type		: 	"POST",
				url			: 	"pages/default/ajax/ajax.php",
				data		:	data,
				dataType	: 	"json",
			}).done(function(response){
				$('.tab-container').html(response.msg);
			}).fail(function(xhr) {
				console.log(xhr.responseText);
			});
		}

		// Load Depense
		function loadDepense(id){
			var data = {
				'controler'		:	'Depense',
				'function'		:	'ByPropriete',
				'params'		:	{
					'id'	:	id
				}
			};
			
			$.ajax({
				type		: 	"POST",
				url			: 	"pages/default/ajax/ajax.php",
				data		:	data,
				dataType	: 	"json",
			}).done(function(response){
				$('.tab-container').html(response.msg);
			}).fail(function(xhr) {
				console.log(xhr.responseText);
			});
		}

		// Load Contrats envers Proprtitaire
		function loadContrat(id){
			var data = {
				'controler'		:	'Propriete_Proprietaire_Location',
				'function'		:	'ByPropriete',
				'params'		:	{
				'id_propriete'		:	id
			}
			};
			
			$.ajax({
				type		: 	"POST",
				url			: 	"pages/default/ajax/ajax.php",
				data		:	data,
				dataType	: 	"json",
			}).done(function(response){
				$('.tab-container').html(response.msg);
			}).fail(function(xhr) {
				console.log(xhr.responseText);
			});
		}

		// Load Contrats envers Proprtitaire
		function loadLocation(id){
			var data = {
				'controler'		:	'Propriete_Location',
				'function'		:	'ByPropriete',
				'params'		:	{
				'id_propriete'		:	id
			}
			};
			
			$.ajax({
				type		: 	"POST",
				url			: 	"pages/default/ajax/ajax.php",
				data		:	data,
				dataType	: 	"json",
			}).done(function(response){
				$('.tab-container').html(response.msg);
			}).fail(function(xhr) {
				console.log(xhr.responseText);
			});
		}

	</script>
</div>

