<html>
    <body>
        <h1>Hi there <?php echo $data['route']['main']['dispatcher']['first_name'];?></h1>
        <img src="<?php echo $data['route']['main']['dispatcher']['picture']['data']['url'];?>" />
        <h2>User data</h2>
        <pre><?php var_dump($data);?></pre>
</body>
</html>
