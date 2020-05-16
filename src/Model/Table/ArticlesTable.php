<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Text; // Text class
use Cake\Event\EventInterface; //cai day can vi se dung callback beforeSave()

/**
 * Articles Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\TagsTable&\Cake\ORM\Association\BelongsToMany $Tags
 *
 * @method \App\Model\Entity\Article newEmptyEntity()
 * @method \App\Model\Entity\Article newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Article[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Article get($primaryKey, $options = [])
 * @method \App\Model\Entity\Article findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Article patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Article[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Article|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Article saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ArticlesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('articles');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Tags', [
            'foreignKey' => 'article_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'articles_tags',
            'dependent' => true // cho Article table biet can xoa luon cac record lien quan neu article bi xoa
        ]);
    }
    // day la 1 ham callback duoc goi truoc khi save
    public function beforeSave(EventInterface $event, $entity, $options){

      if($entity->tag_string){
        $entity->tags = $this->_buildTags($entity->tag_string);
      }

      if($entity->isNew() && !$entity->slug){

        $sluggedTitle = Text::slug($entity->title);
        $entity->slug = substr($sluggedTitle,0,191);
      }
    }
    // ham callback validation
    public function validationDefault(Validator $validator): Validator{
      $validator
        ->allowEmptyString('title', null, false)
        ->minLength('title',10)
        ->maxLength('title',255)
        ->allowEmptyString('body', null, false)
        ->minLength('body',10);

      return $validator;
    }

    /** QUERY BUILDER : https://book.cakephp.org/4/en/orm/query-builder.html
    * Tao finder cho tags
    * $query la mot query builder instance
    * $options se chua 'tags' ma chung ta truyen vao o controller khi goi find('tagged',...)
    */
    public function findTagged(Query $query, array $options){
      $columns = [
        'Articles.id', 'Articles.user_id','Articles.title', 'Articles.body', 'Articles.published', 'Articles.created','Articles.slug'
      ];
      $query = $query->select($columns)->distinct($columns);

      if(empty($options['tags'])){ // neu khong co tags nao lien ket voi articles
        $query->leftJoinWith('Tags')->where(['Tags.title IS'=>null]);
      }else{
        $query->leftJoinWith('Tags')->where(['Tags.title IN'=>$options['tags']]);
      }

      return $query->group(['Articles.id']); // thuc thi query va tra ve ket qua
    }

    protected function _buildTags($tagString){
      // Trim tags
      $newTags = array_map('trim', explode(',', $tagString));
      // Remove all empty tags
      $newTags = array_filter($newTags);
      // Reduce duplicated tags
      $newTags = array_unique($newTags);

      $out = [];
      $query = $this->Tags->find()
          ->where(['Tags.title IN' => $newTags]);

      // Remove existing tags from the list of new tags.
      foreach ($query->extract('title') as $existing) {
          $index = array_search($existing, $newTags);
          if ($index !== false) {
              unset($newTags[$index]);
          }
      }
      // Add existing tags.
      foreach ($query as $tag) {
          $out[] = $tag;
      }
      // Add new tags.
      foreach ($newTags as $tag) {
          $out[] = $this->Tags->newEntity(['title' => $tag]);
      }
      return $out;
    }
}
