<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>adLDAP example</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <h1><a href="index.php">adLDAP example</a></h1>

            <?php if ($exception): ?>
                <p class="alert alert-danger"><?php echo $exception->getMessage() ?></p>
            <?php endif ?>

            <form method="post" action="index.php" class="inline">
                <div class="row">
                    <div class="col-md-6">
                        <h2>Options</h2>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>account_suffix</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="account_suffix" value="<?php echo $options['account_suffix'] ?>"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>base_dn</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="base_dn" value="<?php echo $options['base_dn'] ?>"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>domain_controllers</code></label>
                            </div>
                            <div class="col-md-8">
                                <?php foreach ($options['domain_controllers'] as $dc): ?>
                                    <input type="text" name="domain_controllers[]" class="form-control" value="<?php echo $dc ?>" />
                                <?php endforeach ?>
                                <input type="text" name="domain_controllers[]" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>admin_username</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="admin_username" value="<?php echo $options['admin_username'] ?>"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>admin_password</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="admin_password" value="<?php echo $options['admin_password'] ?>"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>real_primarygroup</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="real_primarygroup" value="<?php echo $options['real_primarygroup'] ?>"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>use_ssl</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="checkbox" name="use_ssl" value="<?php echo $options['use_ssl'] ?>"
                                       class="form-control" <?php if ($options['use_ssl']) {
    echo 'checked';
} ?> />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>use_tls</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="checkbox" name="use_tls" value="<?php echo $options['use_tls'] ?>"
                                       class="form-control" <?php if ($options['use_tls']) {
    echo 'checked';
} ?> />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>recursive_groups</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="checkbox" name="recursive_groups" value="<?php echo $options['recursive_groups'] ?>"
                                       class="form-control" <?php if ($options['recursive_groups']) {
    echo 'checked';
} ?> />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>ad_port</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="ad_port" value="<?php echo $options['ad_port'] ?>"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="inline"><code>sso</code></label>
                            </div>
                            <div class="col-md-8">
                                <input type="checkbox" name="sso" value="<?php echo $options['sso'] ?>"
                                       class="form-control" <?php if ($options['sso']) {
    echo 'checked';
} ?> />
                            </div>
                        </div>

                        <h2>Log In</h2>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="" for="username">Username:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="username" value="<?php echo $username ?>" id="username"
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="" for="password">Password:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="password" id="password" class="form-control" />
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <h2>
                            Info
                            <input type="submit" class="btn btn-primary" value="Get Info" />
                            <a href="index.php" class="btn btn-default">Reset</a>
                        </h2>
                        <?php if (!$adldap): ?>
                        <p class="alert alert-info">
                            Please enter at least a domain controller at left,
                            then hit 'Get Info' above.
                        </p>
                        <?php endif ?>
                        <?php if ($adldap && $adldap->getLdapBind()): ?>
                        <p class="alert alert-success">
                            Bound successfully.
                        </p>
                        <?php endif ?>
                        <?php if ($info): ?>
                        <h3>User info:</h3>
                            <dl>
                                <?php foreach ($info as $key => $val): ?>
                                <?php if (!is_string($key)) {
    continue;
} ?>
                                <dt><?php echo $key ?></dt>
                                <dd><?php var_dump($val) ?></dd>
                                <?php endforeach ?>
                            </dl>
                        <?php endif ?>
                    </div>

                </div>
            </form>


        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body>
</html>
