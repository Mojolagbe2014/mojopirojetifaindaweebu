$(document).ready(function(){
            if (typeof localStorage !== "undefined") {
                if(sessionStorage.supervisor == null || sessionStorage.supervisor ==""){
                    window.location = 'index.html';
                }
            }
            
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
                            var profileDetails = {name:sessionStorage.supervisor, department:sessionStorage.supervisorDept, id:sessionStorage.supervisorEmail};
                            $.each(profileDetails, function(key, value) { 
                                $('form#updateform #'+key).val(value);  
                            });
                        } 
                        
                    }
                });
            }
            
            $('#supervisor').append('<option value="'+sessionStorage.supervisorEmail+'">'+sessionStorage.supervisorEmail+'</option>');
            
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
                formData.append('LoggedInSupervisorId', sessionStorage.supervisorEmail);
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

            $.ajax({
                url: "REST/fetch-projects.php",
                type: 'POST',
                data: {fetchForSupervisor: 'true', supervisor:sessionStorage.supervisorEmail, department:sessionStorage.supervisorDept },
                cache: false,
                success : function(data, status) {
                    if(data.status == "1"){
                        $("#messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>You have '+data.info.length+' project(s) waiting for approval </div>');
                    }
                    else {
                        $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No project available for approval. Please check later.</div>');
                    }
                }
            });
            
            loadMyStudentsProjects();
            function loadMyStudentsProjects(){
                var dataTable = $('#projectlist').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollX": true,
                    "ajax":{
                        url :"REST/manage-projects.php", //employee-grid-data.php",// json datasource
                        type: "post",  // method  , by default get
                        data: {fetchProjects:'true',supervisor:sessionStorage.supervisorEmail, department:sessionStorage.supervisorDept },
                        error: function(){  // error handling
                                $("#projectlist-error").html("");
                                $("#projectlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#projectlist_processing").css("display","none");

                        }
                    }
                } );
            }
            
            var currentStatus;
            $(document).on('click', '.approve-project', function() {
                currentStatus = 'Approve'; if(parseInt($(this).attr('data-status')) == 1) currentStatus = "Disapprove";
                if(confirm("Are you sure you want to "+currentStatus+" this project? Project title: '"+$(this).attr('data-title')+"'")) approvalThisProject($(this).attr('data-id'), $(this).attr('data-status'));
            });
            $(document).on('click', '.delete-project', function() {
                if(confirm("Are you sure you want to delete this project? Project title: '"+$(this).attr('data-title')+"'. Note that project file will also be delete.")) deleteThisProject($(this).attr('data-id'), $(this).attr('data-project-file'));
            });
            $(document).on('click', '.edit-project', function() {
                if(confirm("Are you sure you want to edit this project ["+$(this).attr('data-title')+"] details?")) editProject($(this).attr('data-id'), $(this).attr('data-title'), $(this).find('span#JQDTabstractholder').html(), $(this).attr('data-category'), $(this).attr('data-year'), $(this).attr('data-project-file'));
            });
            
            function approvalThisProject(projectId, status){
                $.ajax({
                    url: "REST/manage-projects.php",
                    type: 'GET',
                    data: {approveProject: 'true', id:projectId, status:status},
                    cache: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox, .messageBox").empty().html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Project has been '+currentStatus+'d.<img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Re-loading...</div>');
                            setInterval(function(){ window.location = "";}, 2000);
                        }
                        else {
                            $("#messageBox, .messageBox").empty().html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Project approval failed.</div>');
                        }
                    }
                });
            }
            
            function deleteThisProject(projectId, projectFile){
                $.ajax({
                    url: "REST/manage-projects.php",
                    type: 'POST',
                    data: {deleteProject: 'true', id:projectId, supervisor:sessionStorage.supervisorEmail, projectFile:projectFile},
                    cache: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Re-loading...</div>');
                            setInterval(function(){ window.location = "";}, 2000);                        }
                        else if(data.status != null && data.status != 1) {
                            $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Project deletion failed. '+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Project deletion failed. '+data+'</div>');
                        }
                    }
                });
            }
            
            function editProject(id, title, abstract, category, year, projectFile){//,
                var formVar = {id:id, title:title, abstract:abstract, category:category, year:year, projectFile:projectFile};
                $.each(formVar, function(key, value) { 
                    if(key == 'projectFile') { $('form #oldFile').val(value); $('form #oldFileComment').text(value).css('color','red');} 
                    else $('form#UpdateProject #'+key).val(value);  
                });
                $('#hiddenUpdateForm').removeClass('hidden');
                CKEDITOR.instances['abstract'].setData(abstract);
            }
            $("form#UpdateProject").submit(function(e){ 
                e.stopPropagation(); 
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('abstract', CKEDITOR.instances['abstract'].getData());
                var alertType = ["danger", "success", "danger", "error"];
                $.ajax({
                url: $(this).attr("action"),
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                async: false,
                success : function(data, status) {
                    $("#hiddenUpdateForm").addClass('hidden');
                    if(data.status === 1) {
                        $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Reloading ...</div>');
                        setInterval(function(){ window.location = "";}, 2000);
                    }
                    else if(data.status === 2 || data.status === 3 || data.status ===0 ) $("#messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                    else $("#messageBox, .messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                },
                error : function(xhr, status) {
                    erroMsg = '';
                    if(xhr.status===0){ erroMsg = 'There is a problem connecting to internet. Please review your internet connection.'; }
                    else if(xhr.status===404){ erroMsg = 'Requested page not found.'; }
                    else if(xhr.status===500){ erroMsg = 'Internal Server Error.';}
                    else if(status==='parsererror'){ erroMsg = 'Error. Parsing JSON Request failed.'; }
                    else if(status==='timeout'){  erroMsg = 'Request Time out.';}
                    else { erroMsg = 'Unknow Error.\n'+xhr.responseText;}          
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Failed. '+erroMsg+'</div>');
                },
                processData: false
            });
                return false;
            });
            $('#cancelEdit').click(function(){ $("#hiddenUpdateForm").addClass('hidden'); });
        });