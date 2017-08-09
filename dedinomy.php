<?php
/*
 * Plugin Name: Mes dédicaces avec Dédinomy BETA
 * Version: 1.1
 * Plugin URI: http://forum.nubox.fr
 * Description: Afficher vos dédicaces facilement depuis l'application Dédinomy. <strong>Vous devez être membre premium</strong> pour pouvoir accèder à cette fonction. Connectez-vous sur <a href="http://manager.nubox.fr">le manager Nubox</a> pour réadapter votre offre.
 * Author: Jean-Baptiste Fournot
 * Author URI: 
 */

add_action('widgets_init','dedinomy_widget');

function dedinomy_widget(){
	register_widget('dedinomy_widget');
}

function myplugin_init() {
 $plugin_dir = basename(dirname(__FILE__)).'/lang';
 load_plugin_textdomain( 'dedi', false, $plugin_dir );
}
add_action('plugins_loaded', 'myplugin_init');

class dedinomy_widget extends WP_widget{

	function dedinomy_widget(){
		$options = array(
			"classname"=>"Dedinomy",
			"description"=>__('Afficher les dédicaces depuis le serveur Dédinomy sur ce widget', 'dedi')
			);
		$this->WP_widget('dedinomy_widget',__('Lister les dédicaces - DEDINOMY', 'dedi'),$options);
	}

	function widget($args,$d){
		extract($args);
		if(isset($d['url'])){

			$apar = $d['url'].'/api.php?method=list_dedi&token='.$d['api'];
			$ch=curl_init($apar);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$statut=curl_exec($ch);
			curl_close($ch);

			$lol = json_decode($statut,true);
			if($lol['statut']=='error'){
				echo _e('<strong>Erreur</strong> - L\'API retourne l\'erreur suivante : <br/><strong>'.$lol['message'].'</strong>', 'dedi');
			}else{
				echo $before_widget;
				echo $before_title.$d['titre'].$after_title;
				foreach ($lol['results'] as $item) {
					echo '<p class="pseudo">'.$item['pseudo'].'</p><p class="message">'.$item['message'].'</p>';
				}
				echo $after_widget;				
			}
		} else {
			echo _e('<strong>Erreur</strong> - Vous devez indiquer l\'url de de votre installation de Dédinomy', 'dedi');
		}
		
	}

	function update($new,$old){
		return $new;
	}

	function form($d){
		$def = array(
			'titre'=>'Dedicaces via Dedinomy',
		);
		$d = wp_parse_args($d,$def);
		?>
		<h2><?php echo _e('Configuration', 'dedi'); ?></h2>
		<p><?php echo _e("L'url de votre serveur dédinomy <strong>est obligatoire</strong> pour accèder aux dédicaces. <strong>Si la fonction list_dedi est privé</strong>, vous devez ajouter votre clef API. <strong>Si elle est publique</strong>, la clef est optionnelle. ", 'dedi'); ?></p>
		<p>
			<label for="<?php echo $this->get_field_name('titre') ;?>"><?php echo _e('Votre titre :', 'dedi'); ?>
			</label>
			<input name="<?php echo $this->get_field_name('titre') ;?>" value="<?php echo $d['titre']; ?>" id="<?php echo $this->get_field_name('titre') ;?>" type="text"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('url') ;?>"><?php echo _e('URL de votre serveur Dédinomy :', 'dedi'); ?>
			</label>
			<input name="<?php echo $this->get_field_name('url') ;?>" value="<?php echo $d['url']; ?>" id="<?php echo $this->get_field_name('url') ;?>" type="text"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('api') ;?>"><?php echo _e('Votre clef API Dédinomy :', 'dedi'); ?>
			</label>
			<input name="<?php echo $this->get_field_name('api') ;?>" value="<?php echo $d['api']; ?>" id="<?php echo $this->get_field_name('api') ;?>" type="text"/>
		</p>
		<?php
	}
}



?>