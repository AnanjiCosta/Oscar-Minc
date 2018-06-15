(function($) {
    $(document).ready(function() {
        oscar.init();
        oscar.mainFormUtils();
        oscar.uploadProcess();
    });

    var oscar = {
        init: function() {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },

        mainFormUtils: function () {
            if( !$('#oscar-main-form').length ){
                return;
            }

            // Masks
            $('div[data-name="mes_ano_de_finalizacao"] input').mask('00/0000');
            $('div[data-name="ano_de_estreia"] input, div[data-name="data_de_estreia"] input').mask('00/00/0000');

            // Chars counter for textarea with limits
            function countChars(limit, input) {
                if( input.parent().parent().find('.acf-label .description .remaining-chars').length === 0 ){
                    input.parent().parent().find('.acf-label .description').append('<span class="remaining-chars">Caracteres restantes: <b></b></span>');
                }
                $('.remaining-chars b').html(( limit - input.val().length ));
            };
            $('div[data-name="breve_sinopse_em_portugues"] textarea').on('keyup', function () {
                var maxLength = parseInt( $(this).attr('maxlength') );
                countChars(maxLength, $(this));
            });

            $('div[data-name="aspect_ratio_outros"] input, div[data-name="formato_de_projecao_outros"] input, div[data-name="som_outros"] input').attr('disabled', true);

            // Disable/Enable inputs, based on other selected options
            function enableOtherInput(input, optionToCheck, inputToEnable) {
                if( input.val() === optionToCheck ){
                    $(inputToEnable).removeAttr('disabled');
                } else {
                    $(inputToEnable).attr('disabled', true);
                }
            }

            $('div[data-name="formato_de_projecao"] select').on('change', function () {
                var inputToEnable = $('div[data-name="formato_de_projecao_outros"] input');
                enableOtherInput($(this), 'Outro', inputToEnable);
            });

            $('div[data-name="aspect_ratio"] select').on('change', function () {
                var inputToEnable = $('div[data-name="aspect_ratio_outros"] input');
                enableOtherInput($(this), 'Outro', inputToEnable);
            });

            $('div[data-name="som"] select').on('change', function () {
                var inputToEnable = $('div[data-name="som_outros"] input');
                enableOtherInput($(this), 'Outro', inputToEnable);
            });
        },

        uploadProcess: function () {
            // Validate movie file
            $(document).on('change', '#oscar-video', function(e) {
                console.log($('#oscar-video')[0].files[0]);
                if ($(this)[0].files[0]) {
                    var errors = validateMovie( $('#oscar-video')[0].files[0] );
                    if( errors.length ){
                        $('#error-alert').removeClass('d-none').html('');
                        $('#oscar-video-upload-btn').attr('disabled', 'disabled');
                        $.each(errors, function (i, error) {
                            $('#error-alert').removeClass('d-none').append('<p><b>Erro: </b>' + error + '</p>');
                        });
                    } else {
                        $('#error-alert').addClass('d-none')
                        $('#oscar-video-name').text($(this)[0].files[0].name);
                        $('#oscar-video-upload-btn').removeAttr('disabled');
                        $('#oscar-video-form .video-drag-area').addClass('ready-to-upload');
                    }
                } else {
                    $('#oscar-video-name').text('');
                    $('#oscar-video-upload-btn').attr('disabled', 'disabled');
                    $('#oscar-video-form .video-drag-area').removeClass('ready-to-upload');
                }

                function validateMovie(movieObj) {
                    var errors = [];
                    if( movieObj.size >  $('#movie_max_size').val() ){
                        errors.push('O tamanho do arquivo excede o limite permitido.');
                    }

                    if( movieObj.type !== 'video/mp4' && movieObj.type !== 'video/avi' ){
                        errors.push('O formato do arquivo não é permitido.');
                    }

                    return errors;
                }
            });


            $("#oscar-video-form").on('submit', function(e) {
                e.preventDefault();
                $('#info-alert, #oscar-video-form .video-drag-area').addClass('d-none');
                $('#oscar-video-form .myprogress').css('width', '0');
                $('#oscar-video-form .msg').text('');

                var oscarVideo = $('#oscar-video').val();
                if (oscarVideo == '') {
                    alert('Por favor, selecione um arquivo para upload.');
                    return;
                }

                var formData = new FormData();
                formData.append('nonce', oscar_minc_vars.upload_file_nonce);
                formData.append('oscarVideo', $('#oscar-video')[0].files[0]);
                formData.append('action', 'upload_oscar_video');
                formData.append('post_id', $('#post_id').val());
                $('#oscar-video-form .msg').text('Upload em progresso, por favor, aguarde...');
                $.ajax({
                    url: oscar_minc_vars.ajaxurl,
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    beforeSend: function () {
                        $('#upload-status').removeClass('hidden');
                    },
                    // this part is progress bar
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                $('#oscar-video-form .myprogress').text(percentComplete + '%');
                                $('#oscar-video-form .myprogress').css('width', percentComplete + '%');
                                if( percentComplete === 100 ){
                                    $('#oscar-video-form .msg').html('Finalizando o processo de envio do filme.');
                                    $('#oscar-video-form .myprogress').removeClass('progress-bar-animated');
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (res) {
                        console.log(res);
                        if( res.success ){
                            $('#oscar-video-form .msg').addClass('success');
                            $('#oscar-video-form .msg').html(res.data);
                            $('#oscar-video-upload-btn').hide();
                        } else {
                            $('#oscar-video-form .myprogress').text('0%');
                            $('#oscar-video-form .myprogress').css('width', '0%');
                            $('#oscar-video-form .msg').html(res.data);
                        }
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        console.error( jqXHR, textStatus, errorThrown );
                    }
                });
            });
        },
    };
})(jQuery);