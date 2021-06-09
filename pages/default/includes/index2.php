<?php session_start() ?>
<div id="page" class="">
	<div class="page-head">
		<div class="title d-flex space-between">
			<div class="name">	Module Name	</div> 
			<div class="actions d-flex">
				<button class="green"><i class="fas fa-plus"></i> Ajouter</button>
			</div>
			
		</div>
		<div class="search d-flex space-between">
			<div class="request d-flex">
				<input type="text" placeholder="chercher" class="mr-5">
				<button class="mr-5 page_search_button" data-use="v_propriete" data-column_style="v_propriete"><i class="fa fa-search"></i></button>
				
				<div class="tags">
					<ul>
						<li id="propriete_category">Catégorie</li>
						<li id="ville">Ville</li>
						<li id="name">Complexe</li>
						<li id="proprietaire">Propriétaire</li>
						<li id="notes">Notes</li>
						<li id="2" class="hide">Column2</li>
						<li id="3" class="hide">Column3</li>
						<li class="show_filters"><i class="fas fa-filter"></i></li>
					</ul>
				</div>
				
			</div>
			<div class="filter">
				<select>
					<option value="-1">Status</option>
					<option value="1">Activé</option>
					<option value="2">Désactivé</option>
				</select>
			</div>
		</div>
		<div class="result d-flex space-between">
			
			<div class="totals">
				Total : 20/1225
			</div>
			
			<div class="d-flex">
				<div class="numbers">
					<select>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="200">100</option>
						<option value="500">500</option>
						<option value="1000">1000</option>
					</select>				
				</div>

				<div class="direction">
					<button><i class="fas fa-angle-left"></i></button>
					<button><i class="fas fa-angle-right"></i></button>					
				</div>

			</div>
		</div>
	</div>
	<div class="page-body" style="padding-left: 10px;">

		<div class="table-container">
			<?php
				$core = $_SESSION["CORE"];
				require_once($core."Prop.php");
				$params = [
					'column_style'	=>	'v_propriete',
					'use'			=>	'v_propriete'

				];
				echo $prop->Table($params);
			?>
		</div>
	</div>
</div>