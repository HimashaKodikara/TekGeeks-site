import{g as a}from"./apexcharts.esm-BO4_2e3J.js";document.addEventListener("DOMContentLoaded",function(){if(document.getElementById("basic-sparkline-chart")){let s=function(e,r,u){const i=[];for(let o=0;o<e;o++)i.push(Math.floor(Math.random()*(u-r+1))+r);return i};const d=document.getElementById("basic-sparkline-chart");d.innerHTML=`
            <div class="row m-0">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Sales</h5>
                                    <span class="text-muted">Monthly</span>
                                </div>
                                <h3 class="text-success mb-0">+24%</h3>
                            </div>
                            <div id="spark1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Revenue</h5>
                                    <span class="text-muted">Quarterly</span>
                                </div>
                                <h3 class="text-success mb-0">+17%</h3>
                            </div>
                            <div id="spark2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Customers</h5>
                                    <span class="text-muted">Daily</span>
                                </div>
                                <h3 class="text-danger mb-0">-3%</h3>
                            </div>
                            <div id="spark3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Orders</h5>
                                    <span class="text-muted">Weekly</span>
                                </div>
                                <h3 class="text-success mb-0">+12%</h3>
                            </div>
                            <div id="spark4"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Engagement</h5>
                                    <span class="text-muted">Monthly</span>
                                </div>
                                <h3 class="text-success mb-0">+8%</h3>
                            </div>
                            <div id="spark5"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Conversion</h5>
                                    <span class="text-muted">Monthly</span>
                                </div>
                                <h3 class="text-success mb-0">+5%</h3>
                            </div>
                            <div id="spark6"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;const n={series:[{data:s(30,30,90)}],chart:{type:"line",height:80,sparkline:{enabled:!0},animations:{enabled:!0,easing:"easeinout",speed:800}},stroke:{width:2,curve:"smooth"},colors:[window.colorMap.primary[500].hex],tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(){return"Sales:"}}},marker:{show:!1}}};new a(document.querySelector("#spark1"),n).render();const l={series:[{data:s(20,10,60)}],chart:{type:"bar",height:80,sparkline:{enabled:!0},animations:{enabled:!0,easing:"easeinout",speed:800}},colors:[window.colorMap.primary[400].hex],plotOptions:{bar:{columnWidth:"60%"}},tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(){return"Revenue:"}}},marker:{show:!1}}};new a(document.querySelector("#spark2"),l).render();const c={series:[{data:s(30,20,50)}],chart:{type:"area",height:80,sparkline:{enabled:!0},animations:{enabled:!0,easing:"easeinout",speed:800}},stroke:{curve:"straight",width:2},fill:{type:"gradient",gradient:{shade:"light",type:"vertical",shadeIntensity:.4,gradientToColors:[window.colorMap.bootstrapVars.bodyBg.hex],inverseColors:!1,opacityFrom:.7,opacityTo:.2,stops:[0,100]}},colors:[window.colorMap.danger[500].hex],tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(){return"Customers:"}}},marker:{show:!1}}};new a(document.querySelector("#spark3"),c).render();const p={series:[{data:s(15,40,100)}],chart:{type:"line",height:80,sparkline:{enabled:!0},animations:{enabled:!0,easing:"easeinout",speed:800}},stroke:{width:2,curve:"straight"},colors:[window.colorMap.success[500].hex],markers:{size:4,colors:[window.colorMap.success[500].hex],strokeColors:"#ffffff",strokeWidth:2,hover:{size:6}},tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(){return"Orders:"}}}}};new a(document.querySelector("#spark4"),p).render();const h={series:[{data:s(15,-10,40).map(e=>e-15)}],chart:{type:"area",height:80,sparkline:{enabled:!0},animations:{enabled:!0,easing:"easeinout",speed:800}},stroke:{curve:"smooth",width:2},fill:{type:"gradient",gradient:{shade:"light",type:"vertical",shadeIntensity:.4,gradientToColors:[window.colorMap.bootstrapVars.bodyBg.hex],inverseColors:!1,opacityFrom:.7,opacityTo:.2,stops:[0,100]}},colors:[window.colorMap.primary[500].hex],tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(){return"Engagement:"}}},marker:{show:!1}}};new a(document.querySelector("#spark5"),h).render();const t=s(24,10,50),m=t.map(e=>e>=40?window.colorMap.primary[500].hex:e>=30?window.colorMap.primary[100].hex:e>=20?window.colorMap.danger[500].hex:window.colorMap.warning[500].hex),v={series:[{data:t}],chart:{type:"bar",height:80,sparkline:{enabled:!0},animations:{enabled:!0,easing:"easeinout",speed:800}},colors:m,plotOptions:{bar:{columnWidth:"80%",distributed:!0}},tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(){return"Conversion:"}}},marker:{show:!1}}};new a(document.querySelector("#spark6"),v).render()}});
