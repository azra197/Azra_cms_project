<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<?php

//use PHPMailer\PHPMailer\PHPMailer;
require __DIR__ . '/vendor/autoload.php';
$errors = [];
$errorMessage = '';
if (!empty($_POST)) {
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $header = "From: " . $_POST['email'];

    if (empty($email)) {
        $errors[] = 'Email is empty';
    }
    if (empty($subject)) {
        $errors[] = 'Subject is empty';
    }
    if (empty($body)) {
        $errors[] = 'Body is empty';
    }
    if (!empty($errors)) {
        $allErrors = join('<br/>', $errors);
        $errorMessage = "<p style='color: red;'>{$allErrors}</p>";
    } else {
        $mail = new PHPMailer();
        // specify SMTP credentials
        $mail->isSMTP();
        $mail->Host = Config::SMTP_HOST;
        $mail->Username = Config::SMTP_USER;
        $mail->Password = Config::SMTP_PASSWORD;
        $mail->Port = Config::SMTP_PORT;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('azra10m@gmail.com', 'Azra Azi');
        $mail->addAddress($email);

        $mail->Subject = $subject;
        // Enable HTML if needed
        $mail->isHTML(true);
        $mail->Body = $body;
        echo $body;
        if ($mail->send()) {
            header('Location: /cms/index.php'); 
        } else {
            $errorMessage = 'Oops, something went wrong. Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}
?>


<!-- Navigation -->

<?php include "includes/navigation.php"; ?>

<!-- Page Content -->

<div class="container">

    <section id="login">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <div class="form-wrap">
                        <h1>Contact</h1>
                        <form role="form" action="" method="post" id="contact-form" autocomplete="off">
                            <?php echo ((!empty($errorMessage)) ? $errorMessage : '') ?>
                            <div class="form-group">
                                <label for="email" class="sr-only">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email">
                            </div>

                            <div class="form-group">
                                <label for="subject" class="sr-only">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter your subject">
                            </div>

                            <div class="form-group">
                                <textarea class="form-control" name="body" id="body" cols="50" rows="10"></textarea>
                            </div>

                            <input type="submit" name="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Submit">
                        </form>

                        <script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
                      <!--  <script>
                            const constraints = {
                                name: {
                                    presence: {
                                        allowEmpty: false
                                    }
                                },
                                email: {
                                    presence: {
                                        allowEmpty: false
                                    },
                                    email: true
                                },
                                message: {
                                    presence: {
                                        allowEmpty: false
                                    }
                                }
                            };
                            const form = document.getElementById('contact-form');
                            form.addEventListener('submit', function(event) {
                                const formValues = {
                                    name: form.elements.name.value,
                                    email: form.elements.email.value,
                                    message: form.elements.message.value
                                };
                                const errors = validate(formValues, constraints);
                                if (errors) {
                                    event.preventDefault();
                                    const errorMessage = Object
                                        .values(errors)
                                        .map(function(fieldValues) {
                                            return fieldValues.join(', ')
                                        })
                                        .join("\n");
                                    alert(errorMessage);
                                }
                            }, false);
                        </script> -->
                    </div>
                </div> <!-- /.col-xs-12 -->
            </div> <!-- /.row -->
        </div> <!-- /.container -->
    </section>


    <hr>

    <?php include "includes/footer.php"; ?>