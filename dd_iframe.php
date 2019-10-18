<?php
/**
 * @package    DD_Iframe
 *
 * @author     HR-IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2019 - 2019 HR-IT-Solutions GmbH
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

jimport('joomla.filesystem.file');

/**
 * Class PlgContentDD_Iframe
 *
 * @since  Version  1.0.0.0
 */
class PlgContentDD_Iframe extends JPlugin
{
	protected $app;

	protected $euprivacy;

	protected $defaultCover;

	protected $coverdiv;

	protected $auto_width;

	protected $auto_center;

	protected $loadbutton;

	protected $autoloadLanguage = true;

	protected $bt_responsiveembed;

	protected $gdpr_text_simple;

	protected $gdpr_lc;

	protected $gdpr_text_on_hover;

	/**
	 * Plugin that place Iframe inside an article.
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 * @since   Version  1.0.0.0
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			return true;
		}

		// Get plugin parameter
		$this->euprivacy          = (int) $this->params->get('euprivacy');
		$this->defaultCover       = htmlspecialchars($this->params->get('defaultcover'), ENT_QUOTES);
		$this->coverdiv           = (int) $this->params->get('coverdiv');
		$this->bt_responsiveembed = (int) $this->params->get('bt_responsiveembed');
		$this->auto_width         = (int) $this->params->get('auto_width');
		$this->auto_center        = (int) $this->params->get('auto_center');
		$this->loadbutton         = (int) $this->params->get('loadbutton');
		$this->gdpr_text_simple   = htmlspecialchars($this->params->get('gdpr_text_simple'), ENT_QUOTES, 'UTF-8');
		$this->gdpr_lc            = (int) $this->params->get('gdpr_lc');
		$this->gdpr_text_on_hover = (int) $this->params->get('gdpr_text_on_hover');

		JHtml::_('stylesheet', 'plg_content_dd_iframe/dd_iframe.css', array('version' => 'auto', 'relative' => true));

		// Expression to search for (dd_iframe)
		$regex = '/{dd_iframe}(.*?){\/dd}/s';

		// Find all instances
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		// Img in htmal and scriptheader
		if ($matches && $this->euprivacy)
		{
			$elementScriptActions = '';

			foreach ($matches as $key => $match)
			{
				$iframe = $this->IframeHTML($article->id . $key, $match[1])['iframe'];
				$elementScriptActions .= $this->buildjQueryElementClickEvent($article->id . $key, $iframe);

				$article->text = str_replace($match[0], $this->IframeHTML($article->id . $key, $match[1])['img'], $article->text);
			}

			$this->setScriptStyleHeader($elementScriptActions);

		}
		// Iframe in html
		elseif($matches)
		{
			foreach ($matches as $key => $match)
			{
				$article->text = str_replace($match[0], $this->IframeHTML($article->id . $key, $match[1])['iframe'], $article->text);
			}
		}
	}

	/**
	 * IframeHTML
	 *
	 * @param   int     $matchID  order number
	 * @param   string  $match    the matches string src:XXXXX:autoplay:1:control:1 etc...
	 *
	 * @return array returns needed html
	 */
	private function IframeHTML($matchID, $match)
	{

		$match = str_replace(array('https://','http://'), '', $match);

		$hasParams = false;
		if (strpos($match, '?') !== false) {
			$hasParams = true;
		}

		$iframeParams = array();
		$matchParts = explode(':', trim($match, ':'));

		if ($matchParts % 2 == 0)
		{
			$this->throwMessageInvalidSnipped();
		}

		// Build associated arraay $iframeParams Array ( [src] => XXXXXXXXXXX [cover] => images/yourimagefile.jpg )
		foreach ($matchParts as $key => $matchPart)
		{
			if ($key % 2 == 0)
			{
				if (isset($matchParts[$key + 1]))
				{
					$iframeParams[$matchPart] = trim($matchParts[$key + 1]);
				}
				else
				{
					$this->throwMessageInvalidSnipped();
				}
			}
		}

		// iframe src
		if (!isset($iframeParams['src']))
		{
			$this->app->enqueueMessage(JText::_('PLG_CONTENT_DD_IFRAME_ALERT_SRC_MISSING'), 'warning');
			$iframeParams['src'] = '';
		}
		// Cover image path
		if (isset($iframeParams['cover']))
		{
			$imagePath = $iframeParams['cover'];
		}
		else
		{
			$imagePath = $this->defaultCover;
		}

		// Img width attribute
		if (isset($iframeParams['width']))
		{
			$width = $iframeParams['width'];
		}
		else
		{
			$width = 640;
		}

		// Img height attribute
		if (isset($iframeParams['height']))
		{
			$height = $iframeParams['height'];
		}
		else
		{
			$height = 315;
		}

		// Img & iframe class attribute
		if (isset($iframeParams['class']))
		{
			$class = $iframeParams['class'];
		}
		else
		{
			$class = '';
		}

		// Iframe url params
		$IframeParams = $this->buildIframeSrcURLParams($iframeParams, $hasParams);

		// GDPR Text
		$gdpr_text = $this->gdpr_text_simple;
		if($gdpr_text || $this->gdpr_lc)
		{
			if($this->gdpr_lc){
				$gdpr_text = JText::_('PLG_CONTENT_DD_IFRAME_GDPR_LC');
			}
			$gdpr_text = '<div class="dd_iframe_gdpr_text">' . $gdpr_text .'</div>';
		}

		// Outer + Auto Classes
		$outerClass = 'dd_iframe_outer';
		if ($this->auto_width)
		{
			$outerClass .= ' auto_width';
		}
		if ($this->auto_center)
		{
			$outerClass .= ' auto_center';
		}
		if($this->gdpr_text_on_hover){
			$outerClass .= ' on_hover';
		}

		$loadbutton = '';
		if($this->loadbutton){
			$loadbuttonStyle  = 'background-image: url(\'' . JUri::root() . 'media/plg_content_dd_iframe/img/loadbutton.png' . '\')';
			$loadbutton       .= '<div class="dd_iframe_loadbutton" style="' . $loadbuttonStyle . '"></div>';
		}

		if ($this->euprivacy && !$this->coverdiv)
		{
			$img = '<div id="dd_iframe' . $matchID . '" ';
				$img .= 'class="' . $outerClass . '">';
			$img .= '<img src="' . $imagePath . '" width="' . $width . '" height="' . $height . '" class="dd_iframe ' . $class . '"/>';
			$img .= $gdpr_text . $loadbutton;
			$img .=	'</div>';
		}
		else if ($this->euprivacy && $this->coverdiv)
		{
			$img = '<div id="dd_iframe' . $matchID . '" ';
			$img .='class="' . $outerClass . '" style="background-image: url(\'' . $imagePath . '\'); width: ' . $width . 'px; height:' . $height . 'px;" class="dd_iframe ' . $class . '">';
			$img .= $gdpr_text . $loadbutton;
			$img .= '</div>';
		}
		else
		{
			$img = '';
		}

		$iframe = '<iframe width="' . $width . '" height="' . $height . '" src="https://' .
			$iframeParams['src'] . $IframeParams . '" class="dd_iframe_frame ' . $class . '"></iframe>';

		if ($this->bt_responsiveembed)
		{
			$iframe = '<div class="embed-responsive embed-responsive-16by9">' . $iframe . '</div>';
		}

		return array("iframe" => $iframe, "img" => $img);

	}

	/**
	 * buildIframeSrcURLParams
	 *
	 * @param   array  $iframeParams params
	 *
	 * @return string  iframe paramter url string &param=value etc...
	 */
	private function buildIframeSrcURLParams($iframeParams, $hasParams = false)
	{

		// Parameter URL
		$paramURL = '';

		$i = 0;

		// Parameter seup
		foreach ($iframeParams as $key => $value)
		{
			if (in_array($key, array('src','cover','widht','height','class')))
			{
				continue;
			}

			if ($i === 0)
			{
				$paramURL .= '?';

				if ($hasParams)
				{
					$paramURL .= '&';
				}
				$paramURL .= $key . '=' . $value;
			}
			else
			{
				$paramURL .= '&' . $key . '=' . $value;
			}

			$i++;
		}

		return $paramURL;
	}

	/**
	 * buildjQueryElementClickEvent
	 *
	 * @param   int     $matchID  order number
	 * @param   string  $iframe   the html iframe snipped
	 *
	 * @return string   the jQuery click event for matchID
	 */
	private function buildjQueryElementClickEvent($matchID, $iframe)
	{
		return '$("#dd_iframe' . $matchID . ' a, #dd_iframe' . $matchID . ' button").click(function(e){
                    e.stopPropagation();; });
				$("#dd_iframe' . $matchID . '").click(function(){
                    $(this).before(\'' . $iframe . '\').remove()
                });';
	}

	/**
	 * setScriptStyleHeader
	 *
	 * @param   string  $elementClickEvents  the jQuery click events for all matchIDs
	 *
	 * @return void
	 */
	private function setScriptStyleHeader($elementClickEvents)
	{
		$doc = JFactory::getDocument();

		$scriptheader = "(function($){ $(document).ready(function() { $elementClickEvents }) })(jQuery);";
		$doc->addScriptDeclaration($scriptheader);

		$doc->addStyleDeclaration('.dd_iframe { cursor: pointer; }');
	}

	/**
	 * throwMessageInvalidSnipped
	 *
	 * @return void
	 */
	private function throwMessageInvalidSnipped()
	{
		$this->app->enqueueMessage(JText::_('PLG_CONTENT_DD_IFRAME_ALERT_INVALID_SNIPPED'), 'warning');
	}

}
