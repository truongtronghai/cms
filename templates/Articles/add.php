<h1><?= __('Add article') ?></h1>
<?= $this->Flash->render(); ?>
<?php
  echo $this->Form->create($article); // tao form co du lieu la entity cua articles
  //echo $this->Form->control('user_id',['type'=>'hidden','value'=>1]);
  echo $this->Form->control('title');
  echo $this->Form->control('body',["row"=>'3']);
  //echo $this->Form->control('tags._ids',['options'=>$tags]); //phia sau dau '.' la label cua control va se la mang id cua tag
  echo $this->Form->control('tag_string', ['type' => 'text']);
  echo $this->Form->button(__("Save"));
  echo $this->Form->end();// ket thuc tao form
 ?>
<span><?= $this->Html->link(__("Back"),['action'=>'index']) ?></span>
