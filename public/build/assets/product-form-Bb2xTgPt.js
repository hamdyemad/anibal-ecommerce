let p=1;const w=4;let g={},d=0,u=null;document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".wizard-step-content").forEach(function(i,o){o===0?i.classList.add("active"):i.classList.remove("active")})});jQuery(document).ready(function(t){console.log("✅ Product form jQuery ready");function i(){console.log("🔧 Attaching event handlers to Select2 dropdowns...");const e=t("#department_id");console.log("📍 Department element found:",e.length>0),console.log("📍 Department has Select2:",e.hasClass("select2-hidden-accessible")),console.log("📍 Department value:",e.val()),t("#department_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#department_id").on("change.productForm","#department_id",function(a){console.log("🎯 Department event triggered:",a.type);const n=t(this).val();console.log("🔄 Department changed:",n);const s=t("#category_id"),r=t("#sub_category_id");if(s.empty().append('<option value="">Loading categories...</option>').prop("disabled",!0).trigger("change"),r.empty().append('<option value="">Select Sub Category</option>').val("").trigger("change"),n){const l=`${window.productFormConfig.categoriesRoute}?department_id=${n}&select2=1`;console.log("🌐 Fetching categories from:",l),fetch(l,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(c=>{if(console.log("📥 Categories response status:",c.status),!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{console.log("✅ Categories API response:",c),s.empty().append('<option value="">Select Category</option>').prop("disabled",!1),c.status&&c.data&&c.data.length>0?(c.data.forEach(m=>{s.append(`<option value="${m.id}">${m.name}</option>`)}),console.log(`✅ Loaded ${c.data.length} categories`)):(console.log("⚠️ No categories found for department:",n),s.append('<option value="">No categories available</option>')),s.trigger("change")}).catch(c=>{console.error("❌ Error loading categories:",c),s.empty().append('<option value="">Error loading categories</option>').prop("disabled",!1).trigger("change")})}else s.empty().append('<option value="">Select Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Department handler attached"),t("#category_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#category_id").on("change.productForm","#category_id",function(a){console.log("🎯 Category event triggered:",a.type);const n=t(this).val();console.log("🔄 Category changed:",n);const s=t("#sub_category_id");if(s.empty().append('<option value="">Loading subcategories...</option>').prop("disabled",!0).trigger("change"),n){const r=`${window.productFormConfig.subCategoriesRoute}?category_id=${n}`;console.log("🌐 Fetching subcategories from:",r),fetch(r,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(l=>{if(console.log("📥 SubCategories response status:",l.status),!l.ok)throw new Error(`HTTP error! status: ${l.status}`);return l.json()}).then(l=>{console.log("✅ SubCategories API response:",l),s.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1),l.status&&l.data&&l.data.length>0?(l.data.forEach(c=>{s.append(`<option value="${c.id}">${c.name}</option>`)}),console.log(`✅ Loaded ${l.data.length} subcategories`)):(console.log("⚠️ No subcategories found for category:",n),s.append('<option value="">No subcategories available</option>')),s.trigger("change")}).catch(l=>{console.error("❌ Error loading subcategories:",l),s.empty().append('<option value="">Error loading subcategories</option>').prop("disabled",!1).trigger("change")})}else s.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Category handler attached"),console.log("✅ All handlers ready!")}function o(){const e=t("#department_id");e.length&&e.hasClass("select2-hidden-accessible")?i():setTimeout(o,100)}setTimeout(o,200),v(p),t("#nextBtn").on("click",function(){console.log("📍 Next button clicked. Current step:",p),b(),p++,p>w&&(p=w),v(p)}),t("#prevBtn").on("click",function(){p--,p<1&&(p=1),v(p)}),t(".wizard-step-nav").on("click",function(){console.log("🖱️ Wizard step clicked!");const e=parseInt(t(this).data("step"));console.log("Clicked step:",e),b(),p=e,v(p)}),t(document).on("click",".edit-step",function(){const e=parseInt(t(this).data("step"));b(),p=e,v(p),t("html, body").animate({scrollTop:t(".card").offset().top-100},300)}),t("#productForm").on("submit",j),t("#configuration_type").on("change",function(){const e=t(this).val();e==="simple"?(t("#simple-product-section").show(),t("#variants-section").hide()):e==="variants"?(t("#simple-product-section").hide(),t("#variants-section").show()):(t("#simple-product-section").hide(),t("#variants-section").hide())}),t("#has_discount").on("change",function(){t(this).is(":checked")?t("#discount-fields").slideDown():(t("#discount-fields").slideUp(),t("#price_before_discount").val(""),t("#offer_end_date").val(""))}),t("#add-stock-row").on("click",function(){N()}),t(document).on("click",".remove-stock-row",function(){t(this).closest("tr").remove(),y(),S(),C()}),t(document).on("input",".stock-quantity",function(){y()}),t("#add-variant-btn").on("click",function(){z()}),t(document).on("click",".remove-variant-btn",function(){t(this).closest(".variant-box").remove(),L(),Q()}),t(document).on("change",".variant-key-select",function(){const e=t(this).closest(".variant-box"),a=t(this).val();a?(e.find(".variant-tree-container").show(),H(e,a)):(e.find(".variant-tree-container").hide(),e.find(".final-variant-id").val(""))}),t(document).on("change",".variant-level-select",function(){const e=t(this).closest(".variant-box"),a=parseInt(t(this).data("level")),n=t(this).val(),s=t(this).find("option:selected").data("has-children");e.find(".final-variant-id").val(""),n?G(e,a,n,s):(e.find(".nested-variant-levels").find("[data-level]").each(function(){parseInt(t(this).data("level"))>a&&t(this).remove()}),E(e))}),t(document).on("click",".add-stock-row-variant",function(){const e=t(this).data("variant-index");W(e)}),t(document).on("click",".remove-variant-stock-row",function(){const e=t(this).closest("tr"),a=t(this).data("variant-index"),n=t(`.variant-stock-rows[data-variant-index="${a}"]`);e.remove(),n.find("tr").each(function(s){t(this).find("td:first").text(s+1)}),n.find("tr").length===0&&(t(`.variant-stock-table-container[data-variant-index="${a}"]`).hide(),t(`.variant-stock-empty-state[data-variant-index="${a}"]`).show()),k(a)}),t(document).on("input",".variant-stock-quantity",function(){const a=t(this).closest("tr").data("variant-index");k(a)}),F(),J(),console.log("✅ Product form navigation initialized")});function F(){console.log("🌍 Loading regions data..."),$.ajax({url:"/api/regions?select2=1",method:"GET",dataType:"json",success:function(t){t.results&&t.results.length>0?(u=t.results,console.log(`✅ Cached ${u.length} regions`)):t.data&&t.data.items&&t.data.items.length>0?(u=t.data.items.map(i=>({id:i.id,text:i.name})),console.log(`✅ Cached ${u.length} regions (alternative format)`)):(console.log("⚠️ No regions from API, using fallback"),u=[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"},{id:6,text:"Beheira"},{id:7,text:"Fayoum"},{id:8,text:"Gharbia"},{id:9,text:"Ismailia"},{id:10,text:"Menofia"}])},error:function(t,i,o){console.log("❌ API error, using fallback regions"),u=[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"},{id:6,text:"Beheira"},{id:7,text:"Fayoum"},{id:8,text:"Gharbia"},{id:9,text:"Ismailia"},{id:10,text:"Menofia"}]}})}function v(t){$(".wizard-step-content").each(function(){$(this).removeClass("active").css("display","none")});const i=$(`.wizard-step-content[data-step="${t}"]`);if(i.length&&i.addClass("active").css("display","block"),Object.keys(g).length>0&&t!==4)for(let o in g){const e=A(o),a=i.find(`[name="${e}"], [name="${e}[]"], [name="${o}"], [name="${o}[]"]`).first();if(a.length){a.addClass("is-invalid"),a.closest(".form-group").find(".error-message").remove();const n=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${g[o][0]}</div>`;if(a.hasClass("select2")||a.data("select2")){const s=a.next(".select2-container");s.length?s.after(n):a.after(n)}else a.after(n)}}$(".wizard-step-nav").removeClass("current"),$(`.wizard-step-nav[data-step="${t}"]`).addClass("current"),$(".wizard-step-nav").each(function(){parseInt($(this).data("step"))<t?$(this).addClass("completed"):$(this).removeClass("completed")}),t===1?$("#prevBtn").hide():$("#prevBtn").show(),t===w?($("#nextBtn").hide(),$("#submitBtn").show()):($("#nextBtn").show(),$("#submitBtn").hide()),$("html, body").animate({scrollTop:$(".card-body").offset().top-100},300)}function b(){$(".error-message").remove(),$(".is-invalid").removeClass("is-invalid"),g={}}function A(t){const i=t.split(".");if(i.length===1)return t;let o=i[0];for(let e=1;e<i.length;e++)o+=`[${i[e]}]`;return o}function T(t){g=t;for(let i in t){const o=t[i],e=A(i),a=$(`[name="${e}"], [name="${e}[]"], [name="${i}"], [name="${i}[]"]`).first();if(a.length){a.addClass("is-invalid");const n=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${o[0]}</div>`;if(a.closest(".form-group").find(".error-message").remove(),a.hasClass("select2")||a.data("select2")){const s=a.next(".select2-container");s.length?s.after(n):a.after(n)}else a.after(n)}}}function V(){$(".wizard-step-content:not(.active)").each(function(){$(this).find("[required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}),$("#simple-product-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")}),$("#variants-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}function P(){$('[data-was-required="true"]').each(function(){$(this).attr("required","required").removeAttr("data-was-required")})}function j(t){t.preventDefault();const i=window.productFormConfig;if(!i)return;b(),V(),typeof LoadingOverlay<"u"&&(LoadingOverlay.show(),LoadingOverlay.progressSequence([30,60,90]));const o=new FormData(this),e=$(this).attr("action");$.ajax({url:e,method:"POST",data:o,processData:!1,contentType:!1,success:function(a){typeof LoadingOverlay<"u"&&LoadingOverlay.animateProgressBar(100),a.success&&(typeof LoadingOverlay<"u"&&LoadingOverlay.showSuccess(a.message||"Product created successfully!","Redirecting..."),setTimeout(function(){window.location.href=i.indexRoute||"/admin/products"},1500))},error:function(a){if(typeof LoadingOverlay<"u"&&LoadingOverlay.hide(),P(),a.status===422){const n=a.responseJSON.errors;T(n)}else alert("An error occurred. Please try again.")}})}function N(){u&&u.length>0?q(u):(console.log("⏳ Regions not loaded yet, waiting..."),setTimeout(function(){u&&u.length>0?q(u):(console.log("⚠️ Using fallback regions"),O())},500))}function O(){const t=[{id:1,name:"Cairo"},{id:2,name:"Alexandria"},{id:3,name:"Giza"},{id:4,name:"Dakahlia"},{id:5,name:"Red Sea"},{id:6,name:"Beheira"},{id:7,name:"Fayoum"},{id:8,name:"Gharbia"},{id:9,name:"Ismailia"},{id:10,name:"Menofia"}],i=$(".stock-row").length;let o='<option value="">Select Region</option>';t.forEach(n=>{o+=`<option value="${n.id}">${n.name}</option>`});const a=`
        <tr class="stock-row">
            <td>${i+1}</td>
            <td>
                <select name="stocks[${i}][region_id]" class="form-control select2-stock" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${i}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(a),S(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),y(),C()}function q(t){const i=$(".stock-row").length;let o='<option value="">Select Region</option>';t.forEach(n=>{const s=n.text||n.name;o+=`<option value="${n.id}">${s}</option>`});const a=`
        <tr class="stock-row">
            <td>${i+1}</td>
            <td>
                <select name="stocks[${i}][region_id]" class="form-control select2-stock" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${i}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(a),S(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),y(),C()}function C(){$(".stock-row").each(function(t){$(this).find("td:first").text(t+1)})}function S(){$(".stock-row").length>0?($("#stock-table-container").show(),$("#stock-empty-state").hide()):($("#stock-table-container").hide(),$("#stock-empty-state").show())}function y(){let t=0;$(".stock-quantity").each(function(){const i=parseInt($(this).val())||0;t+=i}),$("#total-stock").text(t)}function z(){d++;const t=`
        <div class="variant-box card mb-3" data-variant-index="${d}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            Variant ${d}
                        </h6>
                        <small class="text-muted variant-details-path" style="display: none;">
                            <strong>Variant Details:</strong> <span class="variant-path-text"></span>
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="uil uil-trash-alt m-0"></i> Remove
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Variant Configuration Key <span class="text-danger">*</span></label>
                        <select name="variants[${d}][key_id]" class="form-control variant-key-select" required>
                            <option value="">Loading variant keys...</option>
                        </select>
                        <small class="text-muted">Select the type of variant (e.g., Color, Size, Material)</small>
                    </div>
                </div>

                <div class="variant-tree-container" style="display: none;">
                    <div class="nested-variant-levels">
                        <!-- Dynamic variant levels will be added here -->
                    </div>

                    <!-- Hidden input to store the final selected variant ID -->
                    <input type="hidden" name="variants[${d}][value_id]" class="final-variant-id">

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info variant-selection-info" style="display: none;">
                                <i class="uil uil-info-circle"></i>
                                <span class="selection-text">No variant selected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full Product Details Section (shown when final variant is selected) -->
                <div class="variant-product-details mt-3" style="display: none;">

                    <!-- Basic Product Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4">
                                <i class="uil uil-receipt"></i>
                                Product Details
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Variant SKU <span class="text-danger">*</span></label>
                                        <input type="text" name="variants[${d}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${d}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">Enable Discount Offer</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${d}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Price Before Discount</label>
                                                <input type="number" name="variants[${d}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Offer End Date</label>
                                                <input type="date" name="variants[${d}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <!-- Stock Management (reusing existing style) -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="uil uil-package"></i>
                                    Stock per Region
                                </div>
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${d}">
                                    <i class="uil uil-plus"></i> Add New Region
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${d}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">No regions added yet. Click "Add New Region" to start.</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${d}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${d}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">Region</span></th>
                                                    <th><span class="userDatatable-title">Stock Quantity</span></th>
                                                    <th><span class="userDatatable-title">Actions</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${d}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">Total Stock:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${d}">0</span>
                                                    </td>
                                                    <td>-</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;$("#variants-container").append(t),L(),B(d),K(d)}function B(t){const o=$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select");$.ajax({url:"/admin/api/variant-keys",method:"GET",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(e){if(e.success&&e.data){let a='<option value="">Select Variant Key</option>';e.data.forEach(n=>{a+=`<option value="${n.id}">${n.name}</option>`}),o.html(a)}else o.html('<option value="">Error loading keys</option>')},error:function(){o.html('<option value="">Error loading keys</option>')}})}function H(t,i){const o=t.find(".nested-variant-levels"),e=t.find(".variant-selection-info");if(!i){o.empty(),e.hide();return}o.empty(),e.show().find(".selection-text").text("Loading variants..."),I(t,i,null,0)}function I(t,i,o,e){const a=t.find(".nested-variant-levels");$.ajax({url:"/admin/api/variants-by-key",method:"GET",data:{key_id:i,parent_id:o||"root"},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(n){if(n.success&&n.data&&n.data.length>0){const s=e===0?"Root Variants":`Level ${e+1}`,r=`
                    <div class="variant-level mb-3" data-level="${e}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${s} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${e}">
                                    <option value="">Select ${s}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;a.append(r);const l=a.find(`[data-level="${e}"]`).find(".variant-level-select");let c='<option value="">Select variant</option>';n.data.forEach(m=>{const f=m.has_children?" 🌳":"";c+=`<option value="${m.id}" data-has-children="${m.has_children}">${m.name}${f}</option>`}),l.html(c),l.select2({theme:"bootstrap-5",width:"100%"}),n.data.length===1&&!n.data[0].has_children&&l.val(n.data[0].id).trigger("change"),E(t)}else e===0&&t.find(".variant-selection-info").show().find(".selection-text").text("No variants available for this key")},error:function(){console.error("Error loading variant level",e)}})}function G(t,i,o,e){const a=t.find(".nested-variant-levels"),n=t.find(".variant-key-select").val();a.find("[data-level]").each(function(){parseInt($(this).data("level"))>i&&$(this).remove()}),o&&e?I(t,n,o,i+1):o&&M(t,o),E(t)}function M(t,i){t.find(".final-variant-id").val(i),U(t)}function E(t){const i=t.find(".variant-selection-info"),o=i.find(".selection-text"),e=t.find(".final-variant-id").val(),a=t.find(".variant-product-details"),n=t.find(".variant-details-path"),s=t.find(".variant-path-text");if(e){const r=[];if(t.find(".variant-level-select").each(function(){const l=$(this).find("option:selected");l.val()&&r.push(l.text().replace(" 🌳",""))}),r.length>0){const l=r.join(" - ");o.html(`<strong>Selected:</strong> ${r.join(" → ")}`),i.removeClass("alert-info").addClass("alert-success").show(),s.text(l),n.show()}}else o.text("Please select a variant"),i.removeClass("alert-success").addClass("alert-info").show(),n.hide(),a.hide()}function K(t){$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select").select2({theme:"bootstrap-5",width:"100%"})}function U(t,i){t.find(".variant-product-details").show();const e=t.find(".variant-discount-toggle"),a=t.find(".variant-discount-fields");e.on("change",function(){$(this).is(":checked")?a.show():(a.hide(),a.find("input").val(""))})}function W(t){u&&u.length>0?x(t,u):(console.log("⏳ Regions not loaded yet for variant, waiting..."),setTimeout(function(){u&&u.length>0?x(t,u):(console.log("⚠️ Using fallback regions for variant"),X(t))},500))}function x(t,i){const o=$(`.variant-stock-rows[data-variant-index="${t}"]`),e=o.find("tr").length;let a='<option value="">Select Region</option>';i.forEach(function(r){a+=`<option value="${r.id}">${r.text}</option>`});const n=`
        <tr class="variant-stock-row" data-variant-index="${t}" data-row-index="${e}">
            <td class="text-center">${e+1}</td>
            <td>
                <select name="variants[${t}][stock][${e}][region_id]" class="form-control region-select" required>
                    ${a}
                </select>
            </td>
            <td>
                <input type="number" name="variants[${t}][stock][${e}][quantity]"
                       class="form-control variant-stock-quantity" min="0" value="0" required>
            </td>
            <td class="actions">
                <button type="button" class="btn btn-sm btn-danger remove-variant-stock-row m-0" data-variant-index="${t}">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;o.append(n),o.find("tr").last().find(".region-select").select2({theme:"bootstrap-5",width:"100%"}),$(`.variant-stock-table-container[data-variant-index="${t}"]`).show(),$(`.variant-stock-empty-state[data-variant-index="${t}"]`).hide(),k(t)}function X(t){x(t,[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"}])}function k(t){const i=$(`.variant-stock-rows[data-variant-index="${t}"]`),o=$(`.variant-total-stock[data-variant-index="${t}"]`);let e=0;i.find(".variant-stock-quantity").each(function(){const a=parseInt($(this).val())||0;e+=a}),o.text(e)}function L(){$(".variant-box").length>0?($("#variants-empty-state").hide(),$("#variants-container").show()):($("#variants-empty-state").show(),$("#variants-container").hide())}function Q(){$(".variant-box").each(function(t){const i=t+1;$(this).find("h6").html(`<i class="uil uil-cube"></i> Variant ${i}`)})}function J(){$("#add-additional-image-btn").on("click",function(){t()}),$(document).on("click",".remove-additional-image",function(){$(this).closest(".additional-image-item").remove(),o(),e()});function t(){const n=$(".additional-image-item").length+1,s="additional_image_"+Date.now(),r=`
            <div class="col-md-4 mb-3 additional-image-item" data-index="${n}">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Additional Image ${n}</small>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-additional-image">
                                <i class="uil uil-trash-alt m-0"></i>
                            </button>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 11px;">
                            <i class="uil uil-info-circle me-1"></i>
                            Recommended: 800x800px
                        </p>
                        <div class="form-group">
                            <div class="image-upload-wrapper">
                                <div class="image-preview-container" id="${s}-preview-container" data-target="${s}">
                                    <div class="image-placeholder" id="${s}-placeholder">
                                        <i class="uil uil-image-plus"></i>
                                        <p>Click to upload image</p>
                                        <small>Recommended: 800x800px</small>
                                    </div>
                                    <div class="image-overlay">
                                        <button type="button" class="btn-change-image" data-target="${s}">
                                            <i class="uil uil-camera"></i> Change
                                        </button>
                                        <button type="button" class="btn-remove-image" data-target="${s}" style="display: none;">
                                            <i class="uil uil-trash-alt"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                <input type="file" class="d-none image-file-input" id="${s}" name="additional_images[]" accept="image/jpeg,image/png,image/jpg,image/webp" data-preview="${s}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;$("#additional-images-container").append(r),i(s),o()}function i(a){const n=document.getElementById(a),s=document.getElementById(a+"-preview-container"),r=document.getElementById(a+"-placeholder"),l=s.querySelector(".btn-change-image"),c=s.querySelector(".btn-remove-image");s.addEventListener("click",m=>{!m.target.closest(".btn-change-image")&&!m.target.closest(".btn-remove-image")&&n.click()}),l&&l.addEventListener("click",m=>{m.stopPropagation(),m.preventDefault(),n.click()}),n.addEventListener("change",function(m){const f=m.target.files[0];if(f){const R=new FileReader;R.onload=function(D){let _=document.getElementById(a+"-preview-img");if(_)_.src=D.target.result;else{const h=document.createElement("img");h.id=a+"-preview-img",h.className="preview-image",h.src=D.target.result,s.insertBefore(h,r)}r&&(r.style.display="none"),c&&(c.style.display="inline-flex")},R.readAsDataURL(f)}}),c&&c.addEventListener("click",function(m){m.stopPropagation(),n.value="";const f=document.getElementById(a+"-preview-img");f&&f.remove(),r&&(r.style.display="flex"),c.style.display="none"})}function o(){$(".additional-image-item").length>0?($("#additional-images-empty-state").hide(),$("#additional-images-container").show()):($("#additional-images-empty-state").show(),$("#additional-images-container").hide())}function e(){$(".additional-image-item").each(function(a){const n=a+1;$(this).attr("data-index",n),$(this).find("small").text(`Additional Image ${n}`)})}}
