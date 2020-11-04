# Fancy Avatar Generator
Generates cartoon avatars by seed

### Install

`composer require opekunov/stylish-avatar-generator`

### DEMO

http://ava.opekunov.com

### Usage

```
//set TRUE for use Imagik
$manager = new FancyAvatarGenerator(true);

//Seed can be NULL, then it will be generated automatically 
$seed = 'ohMySeed'; 

//Avatar style. Now support only 'flamenko'
$style = 'flamenko'; 

/**
* @var \Intervention\Image\Image $avatar
*/
$avatar = $manager->generateFace($seed, 'flamenko');

//Show as image
echo $avatar->response('jpg', 95);

//...or save
$avatar->save('your/path/filename', 95, 'jpg');
```

### TODO
 - Refactoring
 - Change fixed size
 - Add more styles
 - Add more elements for styles
