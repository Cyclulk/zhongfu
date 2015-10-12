$(function(){
    tinymce.init({
        language: "fr_FR",
        selector: "textarea",
        theme: "modern",
        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        templates: [
            {title: 'Test template 1', content: 'Test 1'},
            {title: 'Test template 2', content: 'Test 2'}
        ],
        valid_styles : { '*' : 'color,font-size,font-weight,font-style,text-decoration' }
    });
});

