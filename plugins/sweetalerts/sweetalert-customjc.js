$(function() {
    Swal = Swal.mixin({
      customClass: {
        confirmButton: 'swal2-styled btn btn-primary',
        cancelButton: 'swal2-styled btn btn-outline-primary'
      },
      buttonsStyling: true,
      padding: '1em'
    });
});