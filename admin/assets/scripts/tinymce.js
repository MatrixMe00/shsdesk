//tiny mce textareas
$(".tinymce").tinymce({
    menubar: false,
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table paste code help wordcount"
    ],
    branding: false,
    toolbar: "undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeFormat | help"
});

$(".admin_tinymce").tinymce({
    menubar: "file edit",
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table paste code help wordcount"
    ],
    branding: false,
    toolbar: "undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeFormat | help"
});

$(".sadmin_tinymce").tinymce({
    menubar: true,
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table paste code help wordcount"
    ],
    branding: false,
    toolbar: "undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeFormat | help"
});