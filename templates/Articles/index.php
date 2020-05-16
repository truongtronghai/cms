<h1><?= __("Articles") ?></h1>
<span><?= $this->Html->link(__("Add article"),['action'=>'add'],['class'=>'button float-right']) ?></span>
<?= $this->Flash->render() ?>
<table>
  <tr>
    <th>
      <?= __("Title") ?>
    </th>
    <th>
      <?= __("Created") ?>
    </th>
    <th>
      &nbsp;
    </th>
  </tr>
  <?php

    foreach($articles as $article){
  ?>
  <tr>
    <td>
      <?php echo $this->Html->link($article->title,['action'=>'view',$article->slug]); ?>
    </td>
    <td>
      <?php echo $article->created->format(DATE_RFC850); ?>
    </td>
    <td>
      <?php
      echo $this->Html->link(__("Edit"),['controller'=>'articles','action'=>'edit',$article->slug]) ;
      echo "|";
      echo $this->Form->postLink(__("Delete"),['controller'=>'articles','action'=>'delete',$article->slug],['confirm'=>__("Are you sure?")]);
      ?>
    </td>
  </tr>
  <?php
    }
  ?>
</table>
<?php
/**
  <?= xyz ?> tuong duong voi <?php echo xyz; ?>
*/
 ?>
