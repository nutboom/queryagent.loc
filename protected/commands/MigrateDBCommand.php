<?php
/*
 * Выполняем все миграции
 */
    class MigrateDBCommand extends CConsoleCommand {
        public function run($args) {
            $runner = new CConsoleCommandRunner();
            $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
            $runner->addCommands($commandPath);
            $args = array(
                array('yiic', 'migrate', '--interactive=false'),
                array('yiic', 'migrate', '--interactive=false', '--migrationPath=user.migrations'),
                array('yiic', 'migrate', '--interactive=false', '--migrationPath=respondent.migrations'),
                array('yiic', 'migrate', '--interactive=false', '--migrationPath=catalog.migrations'),
            );
            //ob_start();
            foreach ($args as $value) {
                $runner->run($value);
                sleep(2);
            }

            //echo htmlentities(ob_get_clean(), null, Yii::app()->charset);
        }
    }
?>
