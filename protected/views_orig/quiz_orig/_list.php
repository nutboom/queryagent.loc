<div class="<?php echo ($quiz->type == "quiz") ? "i" : "m"; ?>-element">
	<div class="pad">
		<div class="panel-group accordion-semi">
			<div class="panel panel-default nCPanel">
				<div class="panel-heading" data-quiz="<?php echo $quiz->quiz_id; ?>" data-showed="0">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion3" href="#ac3-<?php echo $quiz->quiz_id; ?>" class="nCCollapsing collapsed">
							<table style="width:100%" class="i-col-element">
								<tr width="100%">
									<td class="nonborder">
										<span class="glyphicon glyphicon-send" data-toggle="tooltip" title="<?php echo Yii::t("app", "Name of ".$quiz->type); ?>"></span>
									</td>
									<td class="col-sm-4">
										<span><?php echo $quiz->title; ?></span>
									</td>
									<td class="nonborder">
										<b class="fa fa-user" data-toggle="tooltip" title="<?php echo Yii::t("app", "Name of client"); ?>"></b>
									</td>
									<td class="col-sm-2">
										<?php echo $quiz->client->name; ?>
									</td>
									<td class="nonborder">
										<b class="fa fa-calendar" data-toggle="tooltip" title="<?php echo Yii::t("app", "Date created"); ?>"></b>
									</td>
									<td class="col-sm-2">
										<?php echo Current(Explode(" ",$quiz->date_created)); ?>
									</td>
									<td class="nonborder">
										<b class="fa fa-calendar" data-toggle="tooltip" title="<?php echo Yii::t("app", "Date of deadline"); ?>"></b>
									</td>
									<td class="col-sm-2">
										<?php echo (isset($quiz) && $quiz->deadline) ? Utils::unpack_datetime($quiz->deadline) : Yii::t("app", "No Date Filled"); ?>
									</td>
									<td class="nonborder">
										<b class="fa fa-bar-chart-o" data-toggle="tooltip" title="<?php echo Yii::t("app", "Num of closed applications"); ?>"></b>
									</td>
									<td class="col-sm-1">
										<?php echo count($quiz->applications_closed); ?>
									</td>
									<td class="nonborder">
										<b class="fa fa-tasks" data-toggle="tooltip" title="<?php echo Yii::t("app", "Comments"); ?>"></b>
									</td>
									<td class="col-sm-1">
										<?php echo count($quiz->comments); ?>
									</td>
								</tr>
							</table>
						</a>
					</h4>
				</div>
				<div id="ac3-<?php echo $quiz->quiz_id; ?>" class="panel-collapse nCCollapse collapsed collapse">
					<div class="col-sm-12">
						<div id="plot-<?php echo $quiz->quiz_id; ?>" class="graph missions"></div>
					</div>
				</div>
				<div class="nCCollapse extraIcons">
					<div class="col-sm-6">
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/update"); ?>">
								<b class="fa fa-pencil" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Update '.$quiz->type); ?>"></b>
							</a>
						</div>
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/targetAudience"); ?>">
								<b class="fa fa-list" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Target audience'); ?>"></b>
							</a>
						</div>
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/StructureQuiz"); ?>">
								<b class="fa fa-wrench" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Content '.$quiz->type); ?>"></b>
							</a>
						</div>
						<?php if ($quiz->state != Quiz::STATE_EDIT): ?>
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/Applications"); ?>">
								<b class="fa fa-check-circle" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Results'); ?>"></b>
							</a>
						</div>
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/statistics"); ?>">
								<b class="fa fa-signal" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Statistics'); ?>"></b>
							</a>
						</div>
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/comments"); ?>">
								<b class="fa fa-comment" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Comments'); ?>"></b>
							</a>
						</div>
						<div class="col-sm-1">
							<a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/export"); ?>">
								<b class="fa fa-download" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo Yii::t('app','Unload results quiz'); ?>"></b>
							</a>
						</div>
						<?php endif; ?>
						<div class="clearfix"></div>
					</div>
					<div class="statusText col-sm-6">
						<div class="col-sm-12 nCRight-text">
							<span class="statusTextThin"><?php echo Yii::t("app", "Status"); ?>: </span>
							<span><a href="<?php echo $this->createUrl($type."/".$quiz->quiz_id."/update"); ?>">
							<?php
								if ($quiz->state == Quiz::STATE_EDIT) echo Yii::t('app', 'Edit State');
								if ($quiz->state == Quiz::STATE_WORK) echo Yii::t('app', 'Work State');
								if ($quiz->state == Quiz::STATE_FILL) echo Yii::t('app', 'Fill State');
								if ($quiz->state == Quiz::STATE_MODERATION) echo Yii::t('app', 'Moderation State');
								if ($quiz->state == Quiz::STATE_REFUSE) echo Yii::t('app', 'Refuse State');
							?>
							</a></span>

							<?php if (Yii::app()->getModule('user')->isAdmin()) :?>
								&nbsp;&nbsp;&nbsp;
								<span class="statusTextThin"><?php echo Yii::t("app", "User"); ?>: </span>
								<span>
									<a href="<?php echo $this->createUrl("/user/admin/view/id/".$quiz->manager_id); ?>"><?php echo $quiz->manager->username; ?></a>
								</span>
							<?php
								endif;
							?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>