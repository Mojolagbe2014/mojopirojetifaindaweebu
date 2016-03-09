$(document).ready(function(){
            if (typeof localStorage !== "undefined") {
                if(sessionStorage.student == null || sessionStorage.student ==""){
                    window.location = 'index.html';
                }
            }
            $("form#data").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('author', sessionStorage.studentId);
                formData.append('department', sessionStorage.studentDepartment);
                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    async: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else if(data.status != null && data.status != 1){
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                        }
                    },
                    processData: false
                });
                return false;
            });
            
            
            $('#supervisor').empty();
            $.ajax({
                url: "REST/fetch-supervisors-by-dept.php",
                type: 'POST',
                data: {getThisDeptSup:'true', department: sessionStorage.studentDepartment },
                cache: false,
                success : function(data, status) {
                    if(data.status === 0 ){ 
                        $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Supervisor loading error. '+data.msg+'</div>');
                    }
                    if(data.status === 2 ){ 
                        $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No supervisor available for this department</div>');
                         $('#supervisor').append('<option value="">-- No supervisor available for the chosen department --</option>');
                    }
                    if(data.status ===1 && data.info.length === 0){
                        $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No supervisor available for this department.</div>');
                    }
                    else if(data.status ===1 && data.info.length > 0){
                        $('#supervisor').append('<option value="">-- Select your supervisors ID --</option>');
                        $.each(data.info, function(i, item) {
                            $('#supervisor').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    } 

                }
            });
            
            loadProfileDetails();
            function loadProfileDetails(){
                $('#department').empty();
                $.ajax({
                    url: "REST/fetch-departments.php",
                    type: 'POST',
                    data: {},
                    cache: false,
                    success : function(data, status) {
                        if(data.status === 0 ){ 
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Department loading error. '+data.msg+'</div>');
                        }
                        if(data.status === 2 ){ 
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No department available for this department</div>');
                             $('#department').append('<option value="">-- No department available --</option>');
                        }
                        if(data.status ===1 && data.info.length === 0){
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No department available .</div>');
                        }
                        else if(data.status ===1 && data.info.length > 0){
                            $('#department').append('<option value="">-- Select your department --</option>');
                            $.each(data.info, function(i, item) {
                                $('#department').append('<option value="'+item.id+'">'+item.name+'</option>');
                            });
                            var profileDetails = {name:sessionStorage.student, matricNo:sessionStorage.studentMatric, department:sessionStorage.studentDepartment, email:sessionStorage.studentEmail, phone:sessionStorage.studentPhone, id:sessionStorage.studentId};
                            $.each(profileDetails, function(key, value) { 
                                $('form#updateform #'+key).val(value);  
                            });
                        } 
                        
                    }
                });
            }
            
            $("form#updateform").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('update', 'true');
                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    async: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' Signing out...</div>');
                            setInterval(function(){ sessionStorage.clear(); window.location=''; }, 3000);
                        }
                        else if(data.status != null && data.status != 1) {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                        }
                    },
                    processData: false
                });
                return false;
            });
            
            $("form#changepassword").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('LoggedInStudentId', sessionStorage.studentId);
                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    async: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' Signing out...</div>');
                            setInterval(function(){ sessionStorage.clear(); window.location=''; }, 3000);
                        }
                        else if(data.status != null && data.status != 1) {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                        }
                    },
                    processData: false
                });
                return false;
            });
            
            loadMyProjects();
            function loadMyProjects(){
                var dataTable = $('#projectlist').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollX": true,
                    "ajax":{
                        url :"REST/fetch-projects.php", 
                        type: "post",  
                        data: {fetchProjects:'true', author:sessionStorage.studentId },
                        error: function(){  // error handling
                                $("#projectlist-error").html("");
                                $("#projectlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#projectlist_processing").css("display","none");

                        }
                    }
                } );
            }
            
        });