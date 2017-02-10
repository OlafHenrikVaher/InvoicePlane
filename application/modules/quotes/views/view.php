<script>

    $(function () {
        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        $('#quote_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_change_client'); ?>", {
                quote_id: <?php echo $quote_id; ?>,
                client_name: "<?php echo $this->db->escape_str($quote->client_name); ?>"
            });
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_save_quote').click(function () {
            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('quotes/ajax/save'); ?>", {
                    quote_id: <?php echo $quote_id; ?>,
                    quote_number: $('#quote_number').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_date_expires: $('#quote_date_expires').val(),
                    quote_status_id: $('#quote_status_id').val(),
                    quote_password: $('#quote_password').val(),
                    items: JSON.stringify(items),
                    quote_discount_amount: $('#quote_discount_amount').val(),
                    quote_discount_percent: $('#quote_discount_percent').val(),
                    notes: $('#notes').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
                    }
                    else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';
                        if (typeof(resp_errors) == "string") {
                            all_resp_errors = resp_errors;
                        }
                        else {
                            for (var key in resp_errors) {
                                $('#' + key).parent().addClass('has-error');
                                all_resp_errors += resp_errors[key];
                            }
                        }
                        $('#quote_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('quotes/generate_pdf/' . $quote_id); ?>', '_blank');
        });

        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }
            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });
        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });
        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table").sortable({
            helper: fixHelper,
            items: 'tbody'
        });
    });
</script>

<?php echo $modal_delete_quote; ?>
<?php echo $modal_add_quote_tax; ?>

<div id="headerbar">
    <h1>
        <?php
        echo trans('quote') . ' ';
        echo($quote->quote_number ? '#' . $quote->quote_number : $quote->quote_id);
        ?>
    </h1>

    <div class="pull-right btn-group">

        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <?php echo trans('options'); ?> <i class="fa fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="#add-quote-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i>
                        <?php echo trans('add_quote_tax'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php echo trans('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php echo trans('send_email'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_quote_to_invoice"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?php echo trans('quote_to_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_quote"
                       data-quote-id="<?php echo $quote_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php echo trans('copy_quote'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-quote" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i> <?php echo trans('delete'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn_add_row btn btn-sm btn-default">
            <i class="fa fa-plus"></i>
            <?php echo trans('add_new_row'); ?>
        </a>
        <a href="#" class="btn_add_product btn btn-sm btn-default">
            <i class="fa fa-database"></i>
            <?php echo trans('add_product'); ?>
        </a>

        <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_quote">
            <i class="fa fa-check"></i>
            <?php echo trans('save'); ?>
        </a>
    </div>

</div>

<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    <form id="quote_form">
        <div class="quote">
            <div class="cf row">
                <div class="col-xs-12 col-md-5">
                    <div class="pull-left">
                        <h2>
                            <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>">
                                <?php echo $quote->client_name; ?>
                            </a>
                            <?php if ($quote->quote_status_id == 1): ?>
                                <span id="quote_change_client" class="fa fa-edit cursor-pointer small"
                                      data-toggle="tooltip" data-placement="bottom"
                                      title="<?php echo trans('change_client'); ?>">
              </span>
                            <?php endif; ?>
                        </h2>
                        <br>
                        <span>
              <?php echo ($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
              <?php echo ($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
              <?php echo ($quote->client_city) ? $quote->client_city : ''; ?>
              <?php echo ($quote->client_state) ? $quote->client_state : ''; ?>
              <?php echo ($quote->client_zip) ? $quote->client_zip : ''; ?>
              <?php echo ($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
            </span>
                        <br><br>
                        <?php if ($quote->client_phone): ?>
                            <span>
                <strong>
                  <?php echo trans('phone'); ?>:
                </strong>
                                <?php echo $quote->client_phone; ?>
              </span>
                            <br>
                        <?php endif; ?>
                        <?php if ($quote->client_email): ?>
                            <span>
                <strong>
                  <?php echo trans('email'); ?>:
                </strong>
                                <?php echo $quote->client_email; ?>
              </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-xs-12 col-md-7">
                    <div class="details-box">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="quote-properties">
                                    <label for="quote_number">
                                        <?php echo trans('quote'); ?> #
                                    </label>
                                    <div class="controls">
                                        <input type="text" id="quote_number" class="form-control input-sm"
                                            <?php if ($quote->quote_number) : ?> value="<?php echo $quote->quote_number; ?>"
                                            <?php else : ?> placeholder="<?php echo trans('not_set'); ?>"
                                            <?php endif; ?>>
                                    </div>
                                </div>
                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_created">
                                        <?php echo trans('date'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="quote_date_created" id="quote_date_created"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($quote->quote_date_created); ?>"/>
                                        <span class="input-group-addon">
                      <i class="fa fa-calendar fa-fw"></i>
                    </span>
                                    </div>
                                </div>
                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_expires">
                                        <?php echo trans('expires'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="quote_date_expires" id="quote_date_expires"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($quote->quote_date_expires); ?>">
                                        <span class="input-group-addon">
                      <i class="fa fa-calendar fa-fw">
                      </i>
                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="quote-properties">
                                    <label for="quote_status_id">
                                        <?php echo trans('status'); ?>
                                    </label>
                                    <select name="quote_status_id" id="quote_status_id" class="form-control input-sm">
                                        <?php foreach ($quote_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $quote->quote_status_id) { ?>selected="selected"
                                                <?php } ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="quote-properties">
                                    <label for="quote_password">
                                        <?php echo trans('quote_password'); ?>
                                    </label>
                                    <div class="controls">
                                        <input type="text" id="quote_password" class="form-control input-sm"
                                               value="<?php echo $quote->quote_password; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->layout->load_view('quotes/partial_item_table'); ?>
        <hr/>
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        <?php echo trans('notes'); ?>
                    </label>
                    <textarea name="notes" id="notes" rows="3" class="input-sm form-control">
          <?php echo $quote->notes; ?>
        </textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-8">
                <div class="form-group">
                    <label class="control-label">
                        <?php echo trans('attachments'); ?>
                    </label>
                    <br/>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-default fileinput-button">
          <i class="fa fa-plus">
          </i>
          <span>
            <?php echo trans('add_files'); ?>
          </span>
        </span>
                </div>
                <!-- dropzone -->
                <div id="actions" class="col-xs-12 col-sm-12 row">
                    <div class="col-lg-7">
                    </div>
                    <div class="col-lg-5">
                        <!-- The global file processing state -->
                        <div class="fileupload-process">
                            <div id="total-progress" class="progress progress-striped active" role="progressbar"
                                 aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table table-striped files" id="previews">
                        <div id="template" class="file-row">
                            <!-- This is used as the file preview template -->
                            <div>
              <span class="preview">
                <img data-dz-thumbnail/>
              </span>
                            </div>
                            <div>
                                <p class="name" data-dz-name>
                                </p>
                                <strong class="error text-danger" data-dz-errormessage>
                                </strong>
                            </div>
                            <div>
                                <p class="size" data-dz-size>
                                </p>
                                <div class="progress progress-striped active"
                                     role="progressbar" aria-valuemin="0"
                                     aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success"
                                         style="" data-dz-uploadprogress>
                                    </div>
                                </div>
                            </div>
                            <div class="pull-left btn-group">
                                <button data-dz-download class="btn btn-sm btn-primary">
                                    <i class="fa fa-download"></i>
                                    <span><?php echo trans('download'); ?></span>
                                </button>
                                <button data-dz-remove class="btn btn-danger btn-sm delete">
                                    <i class="fa fa-trash-o"></i>
                                    <span><?php echo trans('delete'); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- stop dropzone -->
            </div>
        </div>
        <?php if ($custom_fields): ?>
            <?php $cv = $this->controller->view_data["custom_values"]; ?>
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                        <legend>
                            <?php echo trans('custom_fields'); ?>
                        </legend>
                        <div class="col-xs-6">
                            <?php $i = 0; ?>
                            <?php foreach ($custom_fields as $custom_field): ?>
                                <?php $i++; ?>
                                <?php if ($i % 2 != 0): ?>
                                    <?php print_field($this->mdl_quotes, $custom_field, $cv); ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-xs-6">
                            <?php $i = 0; ?>
                            <?php foreach ($custom_fields as $custom_field): ?>
                                <?php $i++; ?>
                                <?php if ($i % 2 == 0): ?>
                                    <?php print_field($this->mdl_quotes, $custom_field, $cv); ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($quote->quote_status_id != 1) { ?>
            <p class="padded">
                <?php echo trans('guest_url'); ?>:
                <?php echo auto_link(site_url('guest/view/quote/' . $quote->quote_url_key)); ?>
            </p>
        <?php } ?>
    </form>
</div>

<script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/' . $quote->client_id . '/' . $quote->quote_url_key) ?>", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON("<?php echo site_url('upload/upload_file/' . $quote->client_id . '/' . $quote->quote_url_key) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};

                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    createDownloadButton(mockFile, '<?php echo site_url('upload/get_file'); ?>/' + val.fullname);

                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo site_url('upload/get_file'); ?>' + val.fullname);
                    } else {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo base_url(); ?>assets/default/img/favicon.png');
                    }

                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });

    myDropzone.on("addedfile", function (file) {
        myDropzone.emit("thumbnail", file, '<?php echo base_url(); ?>assets/default/img/favicon.png');
        createDownloadButton(file, '<?php echo site_url('upload/get_file/' . $quote->quote_url_key . '_') ?>' + file.name.replace(/\s+/g, '_'));
    });

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    });

    myDropzone.on("sending", function (file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1";
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0";
    });

    myDropzone.on("removedfile", function (file) {
        $.ajax({
            url: "<?php echo site_url('upload/delete_file/' . $quote->quote_url_key) ?>",
            type: "POST",
            data: {'name': file.name}
        });
    });

    function createDownloadButton(file, fileUrl) {
        var downloadButtonList = file.previewElement.querySelectorAll("[data-dz-download]");
        for ($i = 0; $i < downloadButtonList.length; $i++) {
            downloadButtonList[$i].addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.open(fileUrl);
                return false;
            });
        }
    }
</script>
