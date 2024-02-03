

function toast_r (title, message, type)
{
    toastr[type](message, title, {
        closeButton: true,
        showDuration: 300,
        hideDuration: 300,
        positionClass: "toast-top-center",
        progressBar: true,
        timeOut: 3000
    });   
}


$(document).ready(function () {


    $('[data-toggle="tooltip"]').tooltip();
    

    // $(document).on('click', 'a.btn-ajax', function (event) {

    //     event.preventDefault();
    //     var vulnerability_cve_id = $(this).parents('tr[data-cve-id]').data('cve-id');

    //     var $btn = $(this);
    //     $btn._html = $btn.html();
    //     $btn.html('<i class="fa fa-spin fa-spinner"></i>').attr('disabled', 'disabled').addClass('disabled');

    //     $.getJSON($(this).attr('href'), function (json) {

    //         if (json.hasOwnProperty('error')) {
    //             toast_r('Erreur', json.error, 'error');
    //         } else if (json.hasOwnProperty('success')) {
    //             toast_r('Succès', json.success, 'success');
    //             $('tr[data-cve-id="' + vulnerability_cve_id + '"] td.status').html('<span class="badge badge-success">Non vulnérable</span>');
    //             $btn.remove();
    //         }

    //         $btn.html($btn._html).removeAttr('disabled').removeClass('disabled');

    //     });

    // });


    // click on upgrade the module with link
    $(document).on('shown.bs.modal', '#mr_security_admin #modal_upgrade', function (event) {
        
        var $modalBody = $(this).find('.modal-body');        

        // download module
        $.getJSON($modalBody.find('#step_download input[name="url"]').val(), function (json) {
            
            if (json.hasOwnProperty('error')) {
                
                return toast_r('Erreur', json.error, 'error');

            } else if (json.hasOwnProperty('success')) {
                
                // install module
                $('#step_download i.fa').removeClass('fa-spin fa-spinner').addClass('fa-check-circle fa-2x text-success');
                $.getJSON($modalBody.find('#step_install input[name="url"]').val(), function (json) {

                    if (json.hasOwnProperty('error')) {
                        
                        return toast_r('Erreur', json.error, 'error');

                    } else if (json.hasOwnProperty('redirect')) {
                        
                        $('#step_install i.fa').removeClass('fa-spin fa-spinner').addClass('fa-check-circle fa-2x text-success');
                        window.setTimeout(function () {
                            window.location.href = json.redirect;
                        }, 1000);

                    }
                    
                });

            }

        });
    });
    
});