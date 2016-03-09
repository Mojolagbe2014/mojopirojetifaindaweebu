$(document).ready(function(){
            loadProjects();
            function loadProjects(){
                var dataTable = $('#projectlist').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollX": true,
                    "ajax":{
                        url :"REST/fetch-projects.php", 
                        type: "post",  
                        data: {fetchAllProjects:'true'},
                        error: function(){  // error handling
                                $("#projectlist-error").html("");
                                $("#projectlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#projectlist_processing").css("display","none");

                        }
                    }
                } );
            }
        });