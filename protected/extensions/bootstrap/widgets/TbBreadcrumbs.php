<?php
/**
 * TbCrumb class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 */

Yii::import('zii.widgets.CBreadcrumbs');

/**
 * Bootstrap breadcrumb widget.
 * @see http://twitter.github.com/bootstrap/components.html#breadcrumbs
 */
class TbBreadcrumbs extends CBreadcrumbs
{
	/**
	 * @var string the separator between links in the breadcrumbs. Defaults to '/'.
	 */
	public $separator = '/';
	public $config;

	/**
	 * Initializes the widget.
	 */
	public function init() {
		$this->htmlOptions['class'] = '';

		if (
			(
				Yii::app()->controller->id == "quiz"
					&&
				(
					Yii::app()->controller->action->id == "create"
						||
					Yii::app()->controller->action->id == "update"
						||
					Yii::app()->controller->action->id == "collection"
						||
					Yii::app()->controller->action->id == "launch"
				)
			)
			||
			(
				Yii::app()->controller->id == "targetAudience"
					&&
				(
					Yii::app()->controller->action->id == "index"
						||
					Yii::app()->controller->action->id == "create"
						||
					Yii::app()->controller->action->id == "update"
				)
			)
			||
			(
				Yii::app()->controller->id == "structureQuiz"

			)
		) {
			$this->config = "wizard";
		}

		#Yii::app()->controller->module->id == ""
	}

	/**
	 * Renders the content of the widget.
	 * @throws CException
	 */
	public function run() {
		// Hide empty breadcrumbs.
		if (empty($this->links)) return;

		$links = array();

		if ($this->config != "wizard") {
			if (!isset($this->homeLink)) {
				$content = CHtml::link(Yii::t('zii', 'Home'), Yii::app()->homeUrl);
				$links[] = $this->renderItem($content);
			}
			else if ($this->homeLink !== false) {
				$links[] = $this->renderItem($this->homeLink);
			}
		}

		foreach ($this->links as $label => $url) {
			if (is_string($label) || is_array($url)) {
				$ulink = (is_array($url[0])) ? $url[0] : $url ;

				$content = CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $ulink);

				$links[] = $this->renderItem($content, $url[1]);
			}
			else {
				$links[] = $this->renderItem($this->encodeLabel ? CHtml::encode($url) : $url, ($this->config != "wizard") ? true : false);
			}
		}

		echo CHtml::tag('ul', $this->htmlOptions, implode('', $links));
	}

	/**
	 * Renders a single breadcrumb item.
	 * @param string $content the content.
	 * @param boolean $active whether the item is active.
	 * @return string the markup.
	 */
	protected function renderItem($content, $active = false)
	{
		$separator = $active ? '' : '<span class="chevron"></span>';
		$separator = '<span class="chevron"></span>';

		ob_start();
		echo CHtml::openTag('li', $active ? array('class'=>'active') : array());
		echo $content.$separator;
		echo '</li>';
		return ob_get_clean();
	}
}
