$(function() {
    var cookie = getCookie('fileErrors');
    var uid = getParameterByName('uid');
    var page = getParameterByName('p');

    $("#file_size_info").text(fileSizeInfo);
    $("#extension_info").text(extensionsLang.replace('%s', allowedExtensions.join(', ')));

    if (typeof cookie !== "undefined" && (uid !== null && uid.length !== 0)) {
        var decodedCookie = decodeURIComponent(cookie.replace(/\+/g, ' '));
        var json = JSON.parse(decodedCookie);

        if (json.uid == uid && json.fileErrors[0].length > 0) {
            $("#jsErrorBox").css("display", "block");
            $("#errorHeader").text(problemWithAttachments + ':');

            for (var key in json.fileErrors[0]) {
                $(".ticketErrorList").append('<li class="ticketError">' + json.fileErrors[0][key] + '</li>');
            }

            deleteCookie('fileErrors');
        }
    }

    $("#add_file_attachment").click(function(e) {
        fileInputs = $(".attachment_inputs :file").length;

        if (limit > 0 && fileInputs >= limit) {
            $(this).prop('disabled', true);
        } else {
            $(".attachment_inputs").append(
                $("<input/>").attr('type', 'file').attr('name', 'ticket_file[]')
            );
        }

        e.preventDefault();
    });

    $("#submit").click(function(e) {
        var errorHeader = (page == 'viewticket' ? fixBeforeReplying : fixBeforeSubmitting);
        var errorCount = 0;
        var multiple = false;

        $("#jsErrorBox").css("display", "none");
        $(".ticketErrorList").empty();
        $("#errorHeader").text(errorHeader + ':');

        fileInputs = $(".attachment_inputs :file").length;

        if (limit > 0 && fileInputs > limit) {
            $('.ticketErrorList').append('<li class="ticketError">' + maxFileElements.replace("%1", limit) + '</li>')
            ++errorCount;
        } else {
            for (var i = 0; i <= fileInputs-1; ++i) {
                var fileList = $(".attachment_inputs :file").get(i).files;
                var fileIndex = fileList[0];

                // Prevent "multiple" from being added to the input element - check we only have one file.
                if (fileList.length > 1 && !multiple) {
                    $(".ticketErrorList").append('<li class="ticketError">' + multipleFilesSelects + '</li>');
                    multiple = true;
                    ++errorCount;
                }

                if (typeof fileIndex == "undefined") {
                    continue;
                } else {
                    // Seems hacky due to the requirement of including translations.
                    // Make sure the file extension is allowed and the file size is appropriate.
                    if ($.inArray(fileIndex.name.split('.').pop(), allowedExtensions) == -1) {
                        $(".ticketErrorList").append('<li class="ticketError">' + invalidExtensionLang.replace("%1", fileIndex.name) + '</li>');
                        ++errorCount;
                    }

                    if (fileIndex.size > maxFileSize) {
                        $(".ticketErrorList").append('<li class="ticketError">' + invalidSizeLang.replace("%1", fileIndex.name).replace("%2", maxFileSizeUnits) + '</li>');
                        ++errorCount;
                    }
                }
            }
        }

        if (errorCount > 0) {
            $("#jsErrorBox").css("display", "block");
            e.preventDefault();
        }

    });

    $(".downloadAttachmentLink").click(function(e) {
        e.preventDefault();

        var fileName = $(this).text();
        var attachmentId = this.getAttribute('data-id');
        var ticketId = this.getAttribute('data-tid');
        var uniqueId = this.getAttribute('data-uid');

        var url = "?m=tickets&p=download&id=" + attachmentId + "&tid=" + ticketId + "&uid=" + uniqueId + "&type=cleared";

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var downloadUrl = URL.createObjectURL(xhttp.response);
                var a = document.createElement("a");
                document.body.appendChild(a);
                a.style = "display: none";
                a.href = downloadUrl;
                a.download = fileName;
                a.click();
            }
        };
        
        xhttp.open("GET", url, true);
        xhttp.responseType = "blob";
        xhttp.send();
    });

});