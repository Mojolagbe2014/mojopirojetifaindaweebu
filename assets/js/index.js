$(document).ready(function(){
            $("form#data").submit(function(e){ 
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
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'. Please login through the login form.</div>');
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
            
            $("form#login").submit(function(e){ 
                e.stopPropagation();
                e.preventDefault();
                var formDatas = $(this).serialize();
                var userType = $('#userType').val();
                if(userType === 'Supervisor'){ supervisorLogin('REST/supervisor-login.php', formDatas); }
                else if(userType === 'Admin'){ adminLogin('REST/admin-login.php', formDatas); }
                else if(userType === 'Student'){ studentLogin('REST/student-login.php', formDatas); }
                return false;
            });
            
            function supervisorLogin($loginRestUrl, formDatas){
                $.ajax({
                    url: $loginRestUrl,
                    type: 'POST',
                    data: formDatas,
                    cache: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            
                            $.each(data.info, function(i, item) {
                                if (typeof localStorage !== "undefined") {
                                    sessionStorage.supervisor = item.name;
                                    sessionStorage.supervisorEmail = item.id;
                                    sessionStorage.supervisorDept = item.department;
                                }
                            });
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Login Successful! Welcome '+sessionStorage.supervisor+', <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> redirecting... please wait ...</div>');
                            setInterval(function(){ window.location = 'supervisor-area.html'; }, 3000);
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                    }
                });
            }
            
            function adminLogin($loginRestUrl,formDatas ){
                $.ajax({
                    url: $loginRestUrl,
                    type: 'POST',
                    data: formDatas,
                    cache: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            
                            $.each(data.info, function(i, item) {
                                if (typeof localStorage !== "undefined") {
                                    sessionStorage.admin = item.name;
                                    sessionStorage.adminId = item.id;
                                    sessionStorage.adminName = item.userName;
                                }
                            });
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Login Successful! Welcome '+sessionStorage.admin+', <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> redirecting... please wait ...</div>');
                            setInterval(function(){ window.location = 'admin-area.html'; }, 2000);
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                    }
                });
            }
            
            function studentLogin($loginRestUrl,formDatas ){
                $.ajax({
                    url: $loginRestUrl,
                    type: 'POST',
                    data: formDatas,
                    cache: false,
                    success : function(data, status) {
                        if(data.status == "1"){
                            
                            $.each(data.info, function(i, item) {
                                if (typeof localStorage !== "undefined") {
                                    sessionStorage.student = item.name;
                                    sessionStorage.studentId = item.id;
                                    sessionStorage.studentMatric = item.matricNo;
                                    sessionStorage.studentEmail = item.email;
                                    sessionStorage.studentPhone = item.phone;
                                    sessionStorage.studentDepartment = item.department;
                                }
                            });
                            $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Login Successful! Welcome '+sessionStorage.student+', <img src="assets/img/cycling.GIF" width="30" height="30" alt="Ajax Loading"> redirecting... please wait ...</div>');
                            setInterval(function(){ window.location = 'student-area.html'; }, 2000);
                        }
                        else if(data.status != null && data.status != 1){
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                        }
                        else {
                            $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
                        }
                    }
                });
            }
            
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
            
            $("form#resetpassword").submit(function(e){ 
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
            
        });