jQuery(document).ready(function ($) {
    class Batch {

        constructor() {
            this.plugins = $('div [data-plugin]');
            this.current = null;
            this.addListeners();

            this.plugins.each(function () {
                let html = $(this).html()
                $(this).html(html.substring(1).replace("/\n;/g", ''));
            });
        }

        addListeners() {
            let self = this;

            this.plugins.on('click', function () {
                let commands = $(this).html();
                self.current = $(this);
                self.current.addClass('working')

                self.ajaxSetup();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "/parse",
                    data: {
                        commands: commands
                    },
                    //processData: false,
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (msg) {
                        self.current.addClass('done');
                        console.log(msg);
                    }
                });
            })
        }
        ajaxSetup() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    }

    new Batch();
});
