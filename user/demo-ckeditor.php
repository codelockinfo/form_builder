<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="../assets/js/jquery3.6.4.min.js"></script>
    <script src="../assets/js/ckeditor/ckeditor.js"></script>
</head>
<body>
<form class="add_headerdata" method="POST"  action="config.php">
<textarea name="myeditor" class="myeditor"></textarea>
<textarea  name="myeditor31" class="myeditor"></textarea>
<button class="saveForm" type="submit"> Save</button>
</form>
</body>
</html>
<script>
    $('.myeditor').each(function(index,item){
    CKEDITOR.replace(item);

});

    </script> --><!DOCTYPE html>
<html lang="en">
<head>
    <script src="../assets/js/jquery3.6.4.min.js"></script>
    <script src="../assets/js/ckeditor/ckeditor.js"></script>
</head>
<body>
    <form class="add_headerdata" id="addHeaderDataForm">
        <textarea name="myeditor" class="myeditor"></textarea>
        <textarea name="myeditor31" class="myeditor"></textarea>
        <button class="saveForm" type="submit">Save</button>
    </form>

    <script>
        $(document).ready(function() {
            $('.myeditor').each(function(index, item) {
                CKEDITOR.replace(item);
            });

            $('#addHeaderDataForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission
                console.log(CKEDITOR);
                // Update CKEditor instances before getting the values
                for (instance in CKEDITOR.instances) {
                    console.log(CKEDITOR.instances[instance]);
                    console.log(instance);
                    CKEDITOR.instances[instance].updateElement();
                }

                var formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    type: 'POST',
                    url: 'config.php',
                    data: formData,
                    success: function(response) {
                        console.log('Form submitted successfully:', response);
                    },
                    error: function(error) {
                        console.error('Error submitting form:', error);
                    }
                });
            });
        });
    </script>
</body>
</html>
