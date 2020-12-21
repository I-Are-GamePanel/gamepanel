$(function() {
    $(".rateResponse").each(function(){
        var tid = this.getAttribute('data-tid');
        var uid = this.getAttribute('data-uid');
        var reply_id = this.getAttribute('data-reply-id');
        var rating = this.getAttribute('data-rating');

        var inputs = "";

        for (x = 5; x > 0; --x) {
            inputs += "<input class='star star-" + x + "' value='" + x + "' data-tid='" + tid + "' data-uid='" + uid + "' id='reply_" + reply_id + " star-" + x + "' type='radio' name='star'" + (x == rating ? " checked" : "") + ">",
            inputs += "<label class='star star-" + x + "' for='reply_" + reply_id + " star-" + x + "'></label>"
        }

        this.$html = $([
            "<div class='stars'>",
            "   <form action=''>",
                    inputs,
            "   </form>",
            "</div>"
        ].join("\n"));

        $(this).html(this.$html.html());
    });

    $(".ticket_reply_notice").click(function() {
        var state = ($("#toggleNoticeIcon").text() == "+" ? "-" : "+");
        $(".ticket_ReplyBox").slideToggle(function() {
            $("#toggleNoticeIcon").text(state);
        });
    });

    $("input[name=star]").click(function() {
        var data = {
            reply_id: this.getAttribute('id').split(/[ ,]+/)[0].replace(/\D/g, ''),
            tid: this.getAttribute('data-tid'),
            uid: this.getAttribute('data-uid'),
            rating: this.getAttribute('value')
        };

        $.ajax({
            type: "POST",
            url: "home.php?m=tickets&p=rate&type=cleared&data_type=json",
            data: data,
            success: function(data) {
                console.log(data.message);
            },

            dataType: "json",
        });
    });
});