<style>
        html, body {
  background-image: url('../images/Office7.jpg');
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  
}

        </style>
<?php 
$eid = isset($_GET['employee_id']) ?$_GET['employee_id'] :"all";
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d',strtotime(date('Y-m-d')." -1 week"));
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d');
?>
<style>
    .logo-img{
        width:45px;
        height:45px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
    <style>
        html, body {
  background-image: url('../images/Office7.jpg');
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  
}

        </style>
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Attendance List</h3>
        <div class="card-tools align-middle">
            <a class="btn btn-success btn-sm py-1 rounded-0" href="javascript:void(0)" id="print"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
    <div class="card-body">
        <form action="" id="filter">
            <div class="row align-items-end mb-3">
                <div class="form-group col-md-3">
                    <label for="employee_id" class="control-label">Employee</label>
                    <select class="form-select form-select-sm rounded-0 select2" name="employee_id" required>
                        <option value="all" <?php echo $eid == 'all' ? 'selected' : '' ?>>All</option>
                        <?php
                        $employee = $conn->query("SELECT *,(lastname || ', ' || firstname || ' ' || middlename) as `name` FROM employee_list order by `name` asc ");
                        while($row = $employee->fetchArray()): 
                        ?>
                        <option value="<?php echo $row['employee_id'] ?>" <?php echo $eid == $row['employee_id'] ? 'selected' : '' ?>><?php echo $row['employee_code'] . ' '. $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="date_start" class="control-label">Date Start</label>
                    <input type="date" name="date_start" id="date_start" required class="form-control form-control-sm rounded-0" value="<?php echo isset($date_start) ? $date_start : '' ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="date_end" class="control-label">Date End</label>
                    <input type="date" name="date_end" id="date_end" required class="form-control form-control-sm rounded-0" value="<?php echo isset($date_end) ? $date_end : '' ?>">
                </div>
                <div class="form-group col-md-3">
                    <button class="btn btn-sm rounded-0 btn-primary"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
        <div id="outprint">
        <table class="table table-hover table-striped table-bordered" id="list">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="30%">
                <col width="30%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">DateTime</th>
                    <th class="text-center p-0">Employee</th>
                    <th class="text-center p-0">Log Info</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $where = " where date(a.date_created) between '{$date_start}' and '{$date_end}' ";
                if(is_numeric($eid) && $eid > 0){
                    $where .= " and a.employee_id = '{$eid}' ";
                }

                $log_type = array('IN'=>'Time IN','L_OUT'=>'Lunch Break (OUT)','L_IN'=>'After Lunch (IN)','OUT'=>'Time Out');
                $sql = "SELECT a.*,(e.lastname || ', ' || e.firstname || ' ' || e.middlename) as name,e.employee_code,d.name as dept,p.name as pos FROM attendance_list a inner join employee_list e on a.employee_id = e.employee_id inner join department_list d on e.department_id = d.department_id inner join position_list p on e.position_id = p.position_id {$where} order by strftime('%s',a.date_created) desc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-1 px-1 text-end align-middle"><?php echo date("Y-m-d h:i A",strtotime($row['date_created'])) ?></td>
                    <td class="py-1 px-1 lh-1">
                        <small><span class="text-muted">Code:</span> <?php echo $row['employee_code'] ?></small><br>
                        <small><span class="text-muted">Department:</span> <?php echo $row['dept'] ?></small><br>
                        <small><span class="text-muted">Position:</span> <?php echo $row['pos'] ?></small><br>
                        <small><span class="text-muted">Name:</span> <?php echo $row['name'] ?></small>
                    </td>
                    <td class="py-1 px-1 lh-1">
                        <small><span class="text-muted">Type:</span> <?php echo $log_type[$row['att_type']] ?></small><br>
                        <small><span class="text-muted">Device:</span> 
                            <?php if($row['device_type'] == 'desktop'): ?>
                                <span><span class="fa fa-desktop text-primary"></span> Desktop</span>
                            <?php else: ?>
                                <span><span class="fa fa-mobile-alt text-primary"></span> Mobile</span>
                            <?php endif; ?>
                        </small><br>
                        <small><span class="text-muted">IP:</span> <?php echo $row['ip'] ?></small>
                    </td>
                    <th class="text-center py-1 px-2 align-middle">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['attendance_id'] ?>' href="javascript:void(0)">View Details</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['attendance_id'] ?>' data-name = '<?php echo $row['employee_code']." - ".$row['name'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<script>
    var dtable;
    $(function(){
        $('.view_data').click(function(){
            uni_modal("Attendance Log Details",'view_att.php?id='+$(this).attr('data-id'),'large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this attendance log of <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        dtable = $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:3 }
            ]
        })
        $('#print').click(function(){
            dtable.fnDestroy()
            var _p = $('#outprint').clone()
            var _h = $('head').clone()
            var el = $('<div>')
            _p.find('#list td:nth-last-child(1),#list th:nth-last-child(1)').remove()
            if(_p.find('#list tbody tr').length <= 0){
                _p.find('#list tbody').append('<tr><th class="text-center py-1" colspan="4">No data</th></tr>')
            }
            el.append(_h)
            el.append('<h2 class="text-center fw-bold">Attendance Log List</h2>')
            el.append('<hr/>')
            el.append(_p)
            
            var nw = window.open("","_blank","width=1000,height=900,top=50,left=250")
                     nw.document.write(el.html())
                     nw.document.close()
                     setTimeout(() => {
                        nw.print()
                        setTimeout(() => {
                            nw.close()
                            dtable = $('table').dataTable({
                                columnDefs: [
                                    { orderable: false, targets:3 }
                                ]
                            })
                        }, 200);
                     }, 200);
        })
        $('#filter').submit(function(e){
            e.preventDefault();
            location.replace(location.href + "&" + $(this).serialize())
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_attendance',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                consolre.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>