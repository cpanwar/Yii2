<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<h1>Countries</h1>
<ul>
<?php 
echo "<pre>";print_r($customer);die;
foreach ($customer as $value): 
	//echo "<pre>";print_r($value);die;
	?>
    <li>
        <?= Html::encode("{$value['Name']} ") ?>
    </li>
<?php endforeach; ?>
</ul>