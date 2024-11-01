<?php
 /*/
 Plugin Name: VShop
 Plugin URI: http://vshop.fr/
 Description: Intégrez facilement un widget VShop dans vos articles
 Version: 1.5.2
 Author: VShop
 Author URI: http://vshop.fr/
 /*/

function vshopReplace($text) 
{
	$key = get_option('vshop_key');
	$tracking = get_option('vshop_tracking');
    $linkColor = replaceColor(get_option('vshop_linkColor'));
    $textColor = replaceColor(get_option('vshop_textColor'));
    $backgroundColor = replaceColor(get_option('vshop_backgroundColor'));
    $borderColor = replaceColor(get_option('vshop_borderColor'));
    $everywhere = get_option('vshop_everywhere');
    
	preg_match_all('/{vshop(.*?)}/', $text, $matches);
	if (!empty($matches)) {	
		foreach ($matches[1] as $m) {
		    $m = str_replace(':', '', $m);
			$widget = widgetHtml($key, $tracking, $m, $linkColor, $textColor, $backgroundColor, $borderColor);
			if (!empty($m)) {
			    $text = str_replace('{vshop:' . $m . '}', $widget, $text);
			} else {
			    $text = str_replace('{vshop}', $widget, $text);
			}
		}
	}
	
	if (is_single() and !empty($everywhere)) {
	    $text .= widgetHtml($key, $tracking, '', $linkColor, $textColor, $backgroundColor, $borderColor);
	}
	
 	return $text;
}

function widgetHtml($key, $tracking = null, $keyword = null, $linkColor = null, $textColor = null, $backgroundColor = null, $borderColor = null)
{
    $widget = '<div class="vcashW"';
    if (!empty($key)) {
    	$widget .=  ' data-key="' . $key . '"';
    	if (!empty($tracking)) { $widget .=  ' data-tracking="' . $tracking . '"'; }
    }
    if (!empty($keyword)) { $widget .=  ' data-keyword="' . $keyword . '"';	}
    if (!empty($linkColor)) { $widget .=  ' data-linkColor="' . $linkColor . '"';	}
    if (!empty($textColor)) { $widget .=  ' data-textColor="' . $textColor . '"';	}
    if (!empty($backgroundColor)) { $widget .=  ' data-backgroundColor="' . $backgroundColor . '"';	}
    if (!empty($borderColor)) { $widget .=  ' data-borderColor="' . $borderColor . '"';	}
    $widget .=  ' data-layout="small" data-theme="shadow" data-pagination="false" style="width: 100%; height: 218px; max-width:990px;"></div><script type="text/javascript" src="http://vshop.fr/js/w.js"></script>';
    
    return $widget;
}

function vshopSettings() {
    $key = get_option('vshop_key');
    $tracking = get_option('vshop_tracking');
    $linkColor = replaceColor(get_option('vshop_linkColor'));
    $textColor = replaceColor(get_option('vshop_textColor'));
    $backgroundColor = replaceColor(get_option('vshop_backgroundColor'));
    $borderColor = replaceColor(get_option('vshop_borderColor'));
    $everywhere = get_option('vshop_everywhere');
    $everywhereHtml = (!empty($everywhere))? ' checked="checked"' : '';
    $html = 
    '<div class="wrap" style="text-align:left;">
    	<h1>VShop</h1>
        <h2>Instructions</h2>
        <p>Intégrez facilement un widget VShop dans vos articles en ajouter le texte {vshop:produit} ou {vshop} pour avoir un widget contextualisé.</p>
    	<h2>Configuration du plugin</h2>
    	<form action="options.php" method="post" name="options">' . wp_nonce_field('update-options') . '
    	    <table>
    	       <tr>
    	           <th>Clé VShop*</th>
    	           <td><input type="text" name="vshop_key" value="' . $key . '" /></td>
    	       </tr>
    	       <tr>
    	           <th>Nom de la zone</th>
    	           <td><input type="text" name="vshop_tracking" value="' . $tracking . '" /></td>
    		   </tr>
    	       <tr>
    	           <th>Couleur des liens</th>
    	           <td><input type="text" name="vshop_linkColor" value="' . $linkColor . '" /></td>
    		   </tr>
    	       <tr>
    	           <th>Couleur du texte</th>
    	           <td><input type="text" name="vshop_textColor" value="' . $textColor . '" /></td>
    		   </tr>
    	       <tr>
    	           <th>Couleur du fond</th>
    	           <td><input type="text" name="vshop_backgroundColor" value="' . $backgroundColor . '" /></td>
    		   </tr>
    	       <tr>
    	           <th>Couleur de la bordure</th>
    	           <td><input type="text" name="vshop_borderColor" value="' . $borderColor . '" /></td>
    		   </tr>
    	       <tr>
    	           <td><input type="checkbox" name="vshop_everywhere" value="1"' . $everywhereHtml . ' /> Ajouter un widget a chaque fin d\'article</td>
    		   </tr>
    	    </table>
    		<input type="hidden" name="action" value="update" />   
    		<input type="hidden" name="page_options" value="vshop_key,vshop_tracking,vshop_linkColor,vshop_textColor,vshop_backgroundColor,vshop_borderColor,vshop_everywhere" />   	
    		<input type="submit" name="Submit" value="Valider" />
    	</form>
    </div>';
    echo $html;
}

function vshopAdminMenu()
{
	add_menu_page('VShop', 'VShop', 'administrator', 'vshopPlugin', 'vshopSettings');
} 

function replaceColor($color)
{
	return str_replace('#', '', $color);
}

add_action('admin_menu', 'vshopAdminMenu'); 
add_filter('the_content','vshopReplace');