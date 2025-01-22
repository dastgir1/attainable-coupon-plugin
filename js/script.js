// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
$(document).ready(function () { 
    $('#submit').on('click', function () {
        var search = $('#search').val().toLowerCase();
        var coname = $('#companyname').val().toLowerCase();
        $('#coupontable tr').each(function () { 
            var rowText = $(this).text().toLowerCase(); // Get the text of the row
            if (rowText.indexOf(search || coname) > -1) {
                $(this).show(); // Show the row
            } else {
                $(this).hide(); // Hide the row
            }
        });  
    });

    $(document).on('click', '.btn-js', function () {
        if (confirm('Do you realy want to delete this record')) {

            var id = $(this).data('id');
            var element = this;
                $.ajax({
                url: '/auth/coupon/delete.php',
                type: 'POST',
                data: {
                    'delete_btn_set':1,
                    'del_id': id,
                },
                success: function(data){
                    if (data == 1) {
                        $(element).closest('tr').hide();
                        location.href = location.href;
                    } 
                    
                }
            });
		}
	});
});