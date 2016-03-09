$(document).ready(function(){
            if (typeof localStorage !== "undefined") {
                if(sessionStorage.admin == null || sessionStorage.admin ==""){
                    window.location = 'index.html';
                }
            }
            $("form#data").submit(function(e){ 
                e.stopPropagation(); e.preventDefault();
                var formData = new FormData($(this)[0]);
                //formData.append('adminId', sessionStorage.adminId);
                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    async: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'<img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Re-loading...</div>');
                            setInterval(function(){ window.location = "";}, 3000);
                        }
                        else {
                            $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Supervisor registration  failed. '+data.msg+'</div>');
                        }
                    },
                    error : function(xhr, status) {
                        erroMsg = '';
                        if(xhr.status===0){ erroMsg = 'There is a problem connecting to internet. Please review your internet connection.'; }
                        else if(xhr.status===404){ erroMsg = 'Requested page not found.'; }
                        else if(xhr.status===500){ erroMsg = 'Internal Server Error.';}
                        else if(status==='parsererror'){ erroMsg = 'Error. Parsing JSON Request failed.'; }
                        else if(status==='timeout'){  erroMsg = 'Request Time out.';}
                        else { erroMsg = 'Unknow Error.\n'+xhr.responseText;}          
                        
                        $("#messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Supervisor registration  failed. '+erroMsg+'</div>');
                    },
                    processData: false
                });
                return false;
            });
            
            loadDepartments();
            function loadDepartments(){
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
                        } 
                        
                    }
                });
            }
            
            $("form#changepassword").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('LoggedInAdminId', sessionStorage.adminId);
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
            
            $("form#addadmin").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('register', 'true');
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
            
            loadAllSupervisors();
            function loadAllSupervisors(){
                var dataTable = $('#supervisorlist').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollX": true,
                    "ajax":{
                        url :"REST/manage-supervisors.php", //employee-grid-data.php",// json datasource
                        type: "post",  // method  , by default get
                        data: {fetchSupervisors:'true'},
                        error: function(){  // error handling
                                $("#supervisorlist-error").html("");
                                $("#supervisorlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#supervisorlist_processing").css("display","none");

                        }
                    }
                } );
            }
            
            $(document).on('click', '.delete-supervisor', function() {
                if(confirm("Are you sure you want to delete this supervisor ["+$(this).attr('data-name')+"]? ")) deleteSupervisor($(this).attr('data-id'));
            });
            $(document).on('click', '.edit-supervisor', function() {
                if(confirm("Are you sure you want to edit this supervisor ["+$(this).attr('data-name')+"] details?")) editSupervisor($(this).attr('data-id'), $(this).attr('data-name'), $(this).attr('data-department'));
            });
            
            function deleteSupervisor(id){
                $.ajax({
                    url: "REST/manage-supervisors.php",
                    type: 'POST',
                    data: {deleteThisSupervisor:'true', id:id },
                    cache: false,
                    success : function(data, status) {
                        if(data.status === 1){
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Re-loading...</div>');
                            setInterval(function(){ window.location = "";}, 2000);
                        }
                        else if(data.status != null && data.status != 1) {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                        }
                    }
                });
            }
            
            function editSupervisor(id, name, department){//,
                var formVar = {id:id, name:name, department:department};
                $.each(formVar, function(key, value) { 
                    $('form#data #'+key).val(value);  
                });
                $('form#data #passWord').hide().removeAttr('required'); $('form#data #passLabel').hide();
                $('form#data h3').text("Update Supervisor").css('color','black');
                $('form#data #submitsupervisor').html("<i class='icon icon-upload'></i> Update Info");
                $('form#data #supervisorForm').val('update'); $('form#data #labelEmail').append("<span class='red'><em>(Cannot be changed)</em></span>");
            }
            
            loadAllDepartments();
            function loadAllDepartments(){
                var dataTable = $('#departmentlist').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollX": true,
                    "ajax":{
                        url :"REST/manage-departments.php", //employee-grid-data.php",// json datasource
                        type: "post",  // method  , by default get
                        data: {fetchDepartments:'true'},
                        error: function(){  // error handling
                                $("#departmentlist-error").html("");
                                $("#departmentlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#departmentlist_processing").css("display","none");

                        }
                    }
                } );
            }
            
            $("form#adddepartment").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    async: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'<img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Re-loading...</div>');
                            setInterval(function(){ window.location = "";}, 2000);
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
            
            $(document).on('click', '.delete-department', function() {
                if(confirm("Are you sure you want to delete this department ["+$(this).attr('data-name')+"]? ")) deleteDepartment($(this).attr('data-id'));
            });
            $(document).on('click', '.edit-department', function() {
                if(confirm("Are you sure you want to edit this department ["+$(this).attr('data-name')+"] details?")) editDepartment($(this).attr('data-id'), $(this).attr('data-name'), $(this).attr('data-faculty'));
            });
            
            function deleteDepartment(id){
                $.ajax({
                    url: "REST/manage-departments.php",
                    type: 'POST',
                    data: {deleteThisDepartment:'true', id:id },
                    cache: false,
                    success : function(data, status) {
                        if(data.status === 1){
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Re-loading...</div>');
                            setInterval(function(){ window.location = "";}, 2000);
                        }
                        else if(data.status != null && data.status != 1) {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                        }
                    }
                });
            }
            
            function editDepartment(id, name, faculty){//,
                var formVar = {id:id, name:name, faculty:faculty};
                $.each(formVar, function(key, value) { 
                    $('form#adddepartment #'+key).val(value);  
                });
                $('form#adddepartment h3').text("Update Department").css('color','black');
                $('form#adddepartment #submitdepartment').html("<i class='icon icon-upload'></i> Update Department");
                $('form#adddepartment #departmentForm').val('update');
            }
        });