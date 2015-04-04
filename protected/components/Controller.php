<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

        /**
         * Checks if the current route matches with given routes
         * @param array $routes
         * @return bool
         */
        public function isActive($routes = array())
        {
            $routeCurrent = '';
            if ($this->module !== null) {
                $routeCurrent .= sprintf('%s/', $this->module->id);
            }
            $routeCurrent .= sprintf('%s/%s', Yii::app()->request->pathInfo, $this->action->id);
            foreach ($routes as $route) {
                $pattern = sprintf('~%s~', preg_quote($route));
                if (preg_match($pattern, $routeCurrent)) {
                    return true;
                }
            }
            return false;
        }
}