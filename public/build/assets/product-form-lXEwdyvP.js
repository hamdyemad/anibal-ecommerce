let f=1;const b=4;let v={},p=0,h=null;document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".wizard-step-content").forEach(function(s,a){a===0?s.classList.add("active"):s.classList.remove("active")})});jQuery(document).ready(function(e){console.log("✅ Product form jQuery ready"),setTimeout(function(){e("#brand_id, #vendor_id, #department_id, #category_id, #sub_category_id, #tax_id, #configuration_type").each(function(){if(e(this).attr("type")==="hidden"){console.log("⏭️ Skipping Select2 init for hidden input:",e(this).attr("id"));return}var t=e(this).find('option[value=""]').text().trim();console.log("📋 Select2 Init - ID:",e(this).attr("id"),"Placeholder:",t),e(this).select2({theme:"bootstrap-5",width:"100%",allowClear:!1,placeholder:t||"Select An Option"})})},100);function s(t){var i;if(!t){console.log("⚠️ No vendor ID provided");return}console.log("🔄 Fetching departments for vendor:",t);const n=`${((i=window.productFormConfig)==null?void 0:i.departmentsRoute)||"/api/departments"}?vendor_id=${t}`;console.log("🌐 Fetching from:",n),fetch(n,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(l=>{if(console.log("📥 Response status:",l.status),!l.ok)throw new Error(`HTTP error! status: ${l.status}`);return l.json()}).then(l=>{console.log("✅ Departments fetched:",l);const c=e("#department_id");c.empty().append('<option value="">Select Department</option>'),l&&Array.isArray(l)&&l.length>0?(l.forEach(g=>{c.append(`<option value="${g.id}">${g.name}</option>`)}),console.log(`✅ Loaded ${l.length} departments`)):l&&l.data&&Array.isArray(l.data)&&l.data.length>0?(l.data.forEach(g=>{c.append(`<option value="${g.id}">${g.name}</option>`)}),console.log(`✅ Loaded ${l.data.length} departments`)):(console.log("⚠️ No departments found for vendor:",t),c.append('<option value="">No departments available</option>')),c.data("select2")&&c.select2("destroy"),c.select2({theme:"bootstrap-5",width:"100%",allowClear:!1})}).catch(l=>{console.error("❌ Error fetching departments:",l);const c=e("#department_id");c.empty().append('<option value="">Error loading departments</option>'),c.data("select2")&&c.select2("destroy"),c.select2({theme:"bootstrap-5",width:"100%",allowClear:!1})})}e("#vendor_id").on("change",function(){if(e(this).attr("type")==="hidden"){console.log("⏭️ Skipping vendor change handler for hidden input");return}const t=e(this).val();t&&s(t)}),setTimeout(function(){const t=e("#vendor_id"),o=t.val();o&&t.attr("type")==="hidden"&&(console.log("📦 Loading departments for vendor:",o),s(o))},200),setTimeout(function(){_()},500),window.testErrorContainers=function(){console.log("🧪 Testing error containers..."),[1,2].forEach(o=>{const n=`error-translations-${o}-title`,i=e(`#${n}`);console.log(`📝 Container #${n}: exists=${i.length>0}`),i.length>0&&(i.html('<i class="uil uil-exclamation-triangle"></i> Test error message').show(),console.log(`✅ Test error displayed in ${n}`),setTimeout(()=>{i.hide().empty(),console.log(`🧹 Cleared test error from ${n}`)},3e3))}),console.log("💡 Check the form to see test error messages appear and disappear")},window.testTitleError=function(){console.log("🧪 Testing Title (English) error specifically...");const t=e('input[name="translations[1][title]"]'),o=e("#error-translations-1-title");console.log("📝 Title input found:",t.length>0),console.log("📝 Title input element:",t[0]),console.log("📝 Error container found:",o.length>0),console.log("📝 Error container element:",o[0]),o.length>0?(console.log("📝 Container current display:",o.css("display")),console.log("📝 Container current visibility:",o.is(":visible")),console.log("📝 Container classes:",o.attr("class")),console.log("📝 Container style attribute:",o.attr("style")),o.html('<i class="uil uil-exclamation-triangle"></i> TEST: Title is required for English'),o.show(),o.css("display","block"),o.css("visibility","visible"),o.removeClass("d-none").addClass("d-block"),o.attr("style","display: block !important;"),console.log("📝 After force show - display:",o.css("display")),console.log("📝 After force show - visible:",o.is(":visible")),console.log("📝 After force show - style:",o.attr("style")),t.addClass("is-invalid"),console.log("✅ Test error should now be visible under Title (English) field")):console.log("❌ Error container not found for Title (English)")},window.testErrorContainer=function(){console.log("🧪 Direct test of error-translations-1-title container...");const t=e("#error-translations-1-title");console.log("📝 Container found:",t.length>0),console.log("📝 Container element:",t[0]),t.length>0?(console.log("📝 Current display:",t.css("display")),console.log("📝 Current visibility:",t.css("visibility")),console.log("📝 Is visible:",t.is(":visible")),console.log("📝 Current content:",t.html()),console.log("📝 Current classes:",t.attr("class")),console.log("📝 Current style:",t.attr("style")),t.html('<i class="uil uil-exclamation-triangle"></i> DIRECT TEST MESSAGE'),t.show(),t.css("display","block"),t.css("visibility","visible"),t.removeClass("d-none").addClass("d-block"),t.attr("style","display: block !important; visibility: visible !important;"),console.log("📝 After force show:"),console.log("📝 Display:",t.css("display")),console.log("📝 Visibility:",t.css("visibility")),console.log("📝 Is visible:",t.is(":visible")),console.log("📝 Style attr:",t.attr("style")),console.log("✅ Direct test completed - check if message appears")):console.log("❌ Container #error-translations-1-title not found")};function a(){console.log("🔧 Attaching event handlers to Select2 dropdowns..."),e("#vendor_id");const t=e("#department_id"),o=window.productFormConfig.vendorActivitiesMap||{};Object.keys(o).length>0&&(console.log("👤 Admin/Super Admin user detected - hiding departments until vendor selected"),t.find("option[value!=''][data-activities]").hide()),e(document).off("change.productForm","#vendor_id").on("change.productForm","#vendor_id",function(i){console.log("🎯 Vendor changed");const l=e(this).val();l&&s(l)}),e(document).off("change.productForm","#department_id").on("change.productForm","#department_id",function(i){console.log("🎯 Department event triggered:",i.type);const l=e(this).val();console.log("🔄 Department changed:",l);const c=e("#category_id"),g=e("#sub_category_id");if(c.empty().append('<option value="">Loading categories...</option>').prop("disabled",!0).trigger("change"),g.empty().append('<option value="">Select Sub Category</option>').val("").trigger("change"),l){const d=`${window.productFormConfig.categoriesRoute}?department_id=${l}&select2=1`;console.log("🌐 Fetching categories from:",d),fetch(d,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(u=>{if(console.log("📥 Categories response status:",u.status),!u.ok)throw new Error(`HTTP error! status: ${u.status}`);return u.json()}).then(u=>{console.log("✅ Categories API response:",u),c.empty().append('<option value="">Select Category</option>').prop("disabled",!1),u.status&&u.data&&u.data.length>0?(u.data.forEach(C=>{c.append(`<option value="${C.id}">${C.name}</option>`)}),console.log(`✅ Loaded ${u.data.length} categories`)):(console.log("⚠️ No categories found for department:",l),c.append('<option value="">No categories available</option>')),c.trigger("change")}).catch(u=>{console.error("❌ Error loading categories:",u),c.empty().append('<option value="">Error loading categories</option>').prop("disabled",!1).trigger("change")})}else c.empty().append('<option value="">Select Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Department handler attached"),e("#category_id").off("change.productForm select2:select.productForm"),e(document).off("change.productForm","#category_id").on("change.productForm","#category_id",function(i){console.log("🎯 Category event triggered:",i.type);const l=e(this).val();console.log("🔄 Category changed:",l);const c=e("#sub_category_id");if(c.empty().append('<option value="">Loading subcategories...</option>').prop("disabled",!0).trigger("change"),l){const g=`${window.productFormConfig.subCategoriesRoute}?category_id=${l}`;console.log("🌐 Fetching subcategories from:",g),fetch(g,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(d=>{if(console.log("📥 SubCategories response status:",d.status),!d.ok)throw new Error(`HTTP error! status: ${d.status}`);return d.json()}).then(d=>{console.log("✅ SubCategories API response:",d),c.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1),d.status&&d.data&&d.data.length>0?(d.data.forEach(u=>{c.append(`<option value="${u.id}">${u.name}</option>`)}),console.log(`✅ Loaded ${d.data.length} subcategories`)):(console.log("⚠️ No subcategories found for category:",l),c.append('<option value="">No subcategories available</option>')),c.trigger("change")}).catch(d=>{console.error("❌ Error loading subcategories:",d),c.empty().append('<option value="">Error loading subcategories</option>').prop("disabled",!1).trigger("change")})}else c.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Category handler attached"),console.log("✅ All handlers ready!")}function r(){const t=e("#department_id");t.length&&t.hasClass("select2-hidden-accessible")?a():setTimeout(r,100)}setTimeout(r,200),e("#productForm").on("submit",E),m(f),e("#nextBtn").on("click",function(){console.log("📍 Next button clicked. Current step:",f),f++,f>b&&(f=b),m(f)}),e("#prevBtn").on("click",function(){f--,f<1&&(f=1),m(f)}),e(".wizard-step-nav").on("click",function(){console.log("🖱️ Wizard step clicked!");const t=parseInt(e(this).data("step"));console.log("Clicked step:",t),console.log(`🔍 Navigating from step ${f} to step ${t}`),e("#validation-alerts-container").hide().empty(),f=t,m(f)}),e(document).on("click",".edit-step",function(){f=parseInt(e(this).data("step")),m(f),e("html, body").animate({scrollTop:e(".card").offset().top-100},300)}),e("#productForm").on("submit",E),e("#productForm").on("input keyup change","input, textarea, select",function(){const t=e(this),o=t.attr("name");if(o){t.removeClass("is-invalid");let n=null;if(o.includes("translations[")){const i=o.match(/translations\[(\d+)\]\[([^\]]+)\]/);if(i){const l=i[1],c=i[2];n=e(`#error-translations-${l}-${c}`)}}if(!n||!n.length){const i=[`#error-${o}`,`#error-${o.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`,`[data-error-for="${o}"]`];for(const l of i)if(n=e(l),n.length>0)break}n&&n.length&&n.hide().empty(),t.closest(".form-group").find(".error-message:not([id])").remove(),t.siblings(".error-message:not([id])").remove(),(t.hasClass("select2")||t.data("select2"))&&t.next(".select2-container").siblings(".error-message:not([id])").remove(),console.log(`🧹 Cleared error for field: ${o}`)}}),e("#configuration_type").on("change",function(){const t=e(this).val();t==="simple"?(e("#simple-product-section").show(),e("#variants-section").hide()):t==="variants"?(e("#simple-product-section").hide(),e("#variants-section").show()):(e("#simple-product-section").hide(),e("#variants-section").hide())}),e("#has_discount").on("change",function(){e(this).is(":checked")?e("#discount-fields").slideDown():(e("#discount-fields").slideUp(),e("#price_before_discount").val(""),e("#offer_end_date").val(""))}),e("#add-stock-row").on("click",function(){O()}),e(document).on("click",".remove-stock-row",function(){e(this).closest("tr").remove(),y(),R(),L()}),e(document).on("input",".stock-quantity",function(){y()}),e("#add-variant-btn").on("click",function(){P()}),e(document).on("click",".remove-variant-btn",function(){e(this).closest(".variant-box").remove(),q(),W()}),e(document).on("change",".variant-key-select",function(){const t=e(this).closest(".variant-box"),o=e(this).val();o?(t.find(".variant-tree-container").show(),N(t,o)):(t.find(".variant-tree-container").hide(),t.find(".final-variant-id").val(""))}),e(document).on("change",".variant-level-select",function(){const t=e(this).closest(".variant-box"),o=parseInt(e(this).data("level")),n=e(this).val(),i=e(this).find("option:selected").data("has-children");t.find(".final-variant-id").val(""),n?z(t,o,n,i):(t.find(".nested-variant-levels").find("[data-level]").each(function(){parseInt(e(this).data("level"))>o&&e(this).remove()}),x(t))}),e(document).on("click",".add-stock-row-variant",function(){const t=e(this).data("variant-index");X(t)}),e(document).on("click",".remove-variant-stock-row",function(){const t=e(this).closest("tr"),o=e(this).data("variant-index"),n=e(`.variant-stock-rows[data-variant-index="${o}"]`);t.remove(),n.find("tr").each(function(i){e(this).find("td:first").text(i+1)}),n.find("tr").length===0&&(e(`.variant-stock-table-container[data-variant-index="${o}"]`).hide(),e(`.variant-stock-empty-state[data-variant-index="${o}"]`).show()),k(o)}),e(document).on("input",".variant-stock-quantity",function(){const o=e(this).closest("tr").data("variant-index");k(o)}),F(),typeof LoadingOverlay<"u"&&LoadingOverlay.init?(console.log("🔄 Initializing LoadingOverlay..."),LoadingOverlay.init(),console.log("✅ LoadingOverlay initialized")):console.warn("⚠️ LoadingOverlay not available"),console.log("✅ Product form navigation initialized")});function F(){console.log("🌍 Loading regions data..."),$.ajax({url:"/api/area/regions?select2=1",method:"GET",dataType:"json",success:function(s){h=s.data,console.log("✅ Regions loaded successfully:",h)},error:function(s,a,r){console.log("❌ API error, using fallback regions"),h=[{id:1,text:"Cairo",name:"Cairo"},{id:2,text:"Alexandria",name:"Alexandria"},{id:3,text:"Giza",name:"Giza"},{id:4,text:"Luxor",name:"Luxor"},{id:5,text:"Aswan",name:"Aswan"}],console.log("✅ Fallback regions set:",h)}})}function m(e){$(".wizard-step-content").each(function(){$(this).removeClass("active").css("display","none")});const s=$(`.wizard-step-content[data-step="${e}"]`);if(s.length&&s.addClass("active").css("display","block"),Object.keys(v).length>0&&e!==4)for(let a in v){const r=T(a),t=s.find(`[name="${r}"], [name="${r}[]"], [name="${a}"], [name="${a}[]"]`).first();if(t.length){t.addClass("is-invalid");let o=null;if(a.includes("translations.")){const n=a.split(".");if(n.length===3&&n[0]==="translations"){const i=n[1],l=n[2],c=`error-translations-${i}-${l}`;o=$(`#${c}`)}}if(!o||!o.length){const n=t.attr("name"),i=[`#error-${a}`,`#error-${n}`,`#error-${a.replace(/\./g,"-")}`,`#error-${n.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];for(const l of i)if(o=$(l),o.length>0)break}if(o&&o.length){const n=`<i class="uil uil-exclamation-triangle"></i> ${v[a][0]}`;o.html(n).show().css("display","block").removeClass("d-none").addClass("d-block")}else{t.closest(".form-group").find(".error-message:not([id])").remove();const n=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${v[a][0]}</div>`;if(t.hasClass("select2")||t.data("select2")){const i=t.next(".select2-container");i.length?i.after(n):t.after(n)}else t.after(n)}}}$(".wizard-step-nav").removeClass("current"),$(`.wizard-step-nav[data-step="${e}"]`).addClass("current"),$(".wizard-step-nav").each(function(){parseInt($(this).data("step"))<e?$(this).addClass("completed"):$(this).removeClass("completed")}),e===1?$("#prevBtn").hide():$("#prevBtn").show(),e===b?($("#nextBtn").hide(),$("#submitBtn").show()):($("#nextBtn").show(),$("#submitBtn").hide()),$("html, body").animate({scrollTop:$(".card-body").offset().top-100},300)}function T(e){const s=e.split(".");if(s.length===1)return e;let a=s[0];for(let r=1;r<s.length;r++)a+=`[${s[r]}]`;return a}function _(){console.log("🔧 Ensuring all form fields have error containers..."),$("#productForm").find("input, select, textarea").each(function(){const e=$(this),s=e.attr("name");if(!s)return;const a=`error-${s.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`;if($(`#${a}`).length===0){const r=`<div class="error-message text-danger" id="${a}" style="display: none;"></div>`;if(e.hasClass("select2")||e.data("select2")){const t=e.next(".select2-container");t.length?t.after(r):e.after(r)}else e.after(r);console.log(`✅ Created error container for: ${s}`)}})}function D(e){v=e,_();let s='<ul class="mb-0">';for(let a in e){const r=e[a];r.forEach(n=>{s+=`<li class="mb-2">${n}</li>`});const t=T(a),o=$(`[name="${t}"], [name="${t}[]"], [name="${a}"], [name="${a}[]"]`).first();if(console.log(`🔍 Looking for field: ${a} -> ${t}, found: ${o.length>0}`),a.includes("translations.")&&a.includes(".title")){console.log(`📝 Translation title field detected: ${a}`);const n=`error-translations-${a.split(".")[1]}-title`;console.log(`📝 Expected error container ID: ${n}`),console.log("📝 Container exists:",$(`#${n}`).length>0),console.log("📝 Container element:",$(`#${n}`)[0])}if(o.length){o.addClass("is-invalid");let n=null;if(a.includes("translations.")){const i=a.split(".");if(i.length===3&&i[0]==="translations"){const l=i[1],c=i[2],g=`error-translations-${l}-${c}`;n=$(`#${g}`),console.log(`🔍 Looking for translation container: #${g}, found: ${n.length>0}`),n.length>0&&console.log("📝 Container element:",n[0])}}if(!n||!n.length){const i=o.attr("name"),l=[`#error-${a}`,`#error-${i}`,`#error-${a.replace(/\./g,"-")}`,`#error-${i.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];console.log("🔍 Trying fallback selectors:",l);for(const c of l)if(n=$(c),n.length>0){console.log(`✅ Found with selector: ${c}`);break}}if(n&&n.length){const i=`<i class="uil uil-exclamation-triangle"></i> ${r[0]}`;console.log(`✅ Using existing container for ${a}, setting content: ${i}`),n.html(i),n.show(),n.css("display","block"),n.css("visibility","visible"),n.removeClass("d-none").addClass("d-block"),n.attr("style","display: block !important;"),console.log(`✅ Container after update - visible: ${n.is(":visible")}, display: ${n.css("display")}`)}else{const i=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${r[0]}</div>`;if(o.closest(".form-group").find(".error-message:not([id])").remove(),o.hasClass("select2")||o.data("select2")){const l=o.next(".select2-container");l.length?l.after(i):o.after(i)}else o.after(i);console.log(`✅ Created new error message for field: ${a}`)}}else console.log(`❌ Field element not found for: ${a} (${t})`)}if(s+="</ul>",Object.keys(e).length>0){const r=`
            <div class="alert alert-danger alert-dismissible fade show validation-errors-alert" role="alert">
                <div class="d-flex align-items-start">
                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 18px; margin-top: 2px;"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">${document.documentElement.dir==="rtl"||document.documentElement.lang==="ar"||$("html").attr("lang")==="ar"?"يرجى تصحيح الأخطاء التالية:":"Please correct the following errors:"}</h6>
                        ${s}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;$(".validation-errors-alert").remove();const t=$("#validation-alerts-container");t.length?(console.log("✅ Adding alert to validation-alerts-container"),t.html(r).show()):(console.log("⚠️ validation-alerts-container not found, using fallback"),$(".card-body").prepend(r)),setTimeout(()=>{const o=$(".validation-errors-alert");o.length?(o.show(),console.log("✅ Alert should now be visible"),$("html, body").animate({scrollTop:o.offset().top-100},300)):console.log("❌ Alert element not found after creation")},100)}}function I(){$(".wizard-step-content:not(.active)").each(function(){$(this).find("[required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}),$("#simple-product-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")}),$("#variants-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}function S(){$('[data-was-required="true"]').each(function(){$(this).attr("required","required").removeAttr("data-was-required")})}function E(e){console.log("Form submission started"),e.preventDefault();const s=window.productFormConfig;if(!s){console.error("productFormConfig is not defined");return}if(I(),typeof LoadingOverlay<"u")LoadingOverlay.overlay||(console.log("Initializing LoadingOverlay..."),LoadingOverlay.init());else{console.error("LoadingOverlay is not defined");return}const a=new FormData(this),r=$(this).attr("action"),o=$('input[name="_method"][value="PUT"]').length>0?s.updatingProduct||"Updating product...":s.creatingProduct||"Creating product...",n=document.getElementById("loadingOverlay");n&&(n.querySelector(".loading-text").textContent=o,n.querySelector(".loading-subtext").textContent=s.pleaseWait||"Please wait..."),LoadingOverlay.show(),LoadingOverlay.animateProgressBar(30,300).then(()=>fetch(r,{method:"POST",body:a,headers:{"X-Requested-With":"XMLHttpRequest",Accept:"application/json"}})).then(i=>(LoadingOverlay.animateProgressBar(60,200),i.ok?i.json():i.json().then(l=>{throw l}))).then(i=>LoadingOverlay.animateProgressBar(90,200).then(()=>i)).then(i=>LoadingOverlay.animateProgressBar(100,200).then(()=>{S();const l=$('input[name="_method"][value="PUT"]').length>0,c=i.message||(l?s.productUpdated:s.productCreated)||"Product saved successfully!";LoadingOverlay.showSuccess(c,s.redirecting||"Redirecting..."),setTimeout(()=>{window.location.href=i.redirect||s.indexRoute||"/admin/products"},1500)})).catch(i=>{if(LoadingOverlay.hide(),S(),console.log("Error:",i),i.errors)console.log("Validation errors:",i.errors),D(i.errors);else{const l=i.message||"An error occurred. Please try again.";console.error("Error message:",l),alert(l)}})}function O(){V(h)}function V(e){const s=$(".stock-row").length;let a='<option value="">Select Region</option>';e.forEach(o=>{const n=o.text||o.name;a+=`<option value="${o.id}">${n}</option>`});const t=`
        <tr class="stock-row">
            <td>${s+1}</td>
            <td>
                <select name="stocks[${s}][region_id]" class="form-control select2-stock" required>
                    ${a}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${s}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(t),R(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),y(),L()}function L(){$(".stock-row").each(function(e){$(this).find("td:first").text(e+1)})}function R(){$(".stock-row").length>0?($("#stock-table-container").show(),$("#stock-empty-state").hide()):($("#stock-table-container").hide(),$("#stock-empty-state").show())}function y(){let e=0;$(".stock-quantity").each(function(){const s=parseInt($(this).val())||0;e+=s}),$("#total-stock").text(e)}function P(){p++;const e=window.productFormConfig,s=`
        <div class="variant-box card mb-3" data-variant-index="${p}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            ${e.variantNumber} ${p}
                        </h6>
                        <small class="text-muted variant-details-path" style="display: none;">
                            <strong>${e.variantDetails}:</strong> <span class="variant-path-text"></span>
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="uil uil-trash-alt m-0"></i> ${e.remove}
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">${e.selectVariantKey} <span class="text-danger">*</span></label>
                        <select name="variants[${p}][key_id]" class="form-control variant-key-select" required>
                            <option value="">${e.loadingVariantKeys}</option>
                        </select>
                        <small class="text-muted">${e.selectVariantKeyHelper}</small>
                    </div>
                </div>

                <div class="variant-tree-container" style="display: none;">
                    <div class="nested-variant-levels">
                        <!-- Dynamic variant levels will be added here -->
                    </div>

                    <!-- Hidden input to store the final selected variant ID -->
                    <input type="hidden" name="variants[${p}][value_id]" class="final-variant-id">

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
                                ${e.productDetails}
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${e.variantSku} <span class="text-danger">*</span></label>
                                        <input type="text" name="variants[${p}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${e.price} <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${p}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">${e.enableDiscountOffer}</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${p}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${e.priceBeforeDiscount}</label>
                                                <input type="number" name="variants[${p}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${e.offerEndDate}</label>
                                                <input type="date" name="variants[${p}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
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
                                    ${e.stockPerRegion}
                                </div>
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${p}">
                                    <i class="uil uil-plus"></i> ${e.addNewRegion}
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${p}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">${e.noRegionsAddedYet}</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${p}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${p}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">${e.region}</span></th>
                                                    <th><span class="userDatatable-title">${e.stockQuantity}</span></th>
                                                    <th><span class="userDatatable-title">${e.actionsLabel}</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${p}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">${e.totalStock}:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${p}">0</span>
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
    `;$("#variants-container").append(s),q(),j(p),M(p)}function j(e){const a=$(`.variant-box[data-variant-index="${e}"]`).find(".variant-key-select");$.ajax({url:"/admin/api/variant-keys",method:"GET",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(r){if(r.success&&r.data){let t='<option value="">Select Variant Key</option>';r.data.forEach(o=>{t+=`<option value="${o.id}">${o.name}</option>`}),a.html(t)}else a.html('<option value="">Error loading keys</option>')},error:function(){a.html('<option value="">Error loading keys</option>')}})}function N(e,s){const a=e.find(".nested-variant-levels"),r=e.find(".variant-selection-info");if(!s){a.empty(),r.hide();return}a.empty(),r.show().find(".selection-text").text("Loading variants..."),A(e,s,null,0)}function A(e,s,a,r){const t=e.find(".nested-variant-levels"),o=window.productFormConfig;$.ajax({url:"/admin/api/variants-by-key",method:"GET",data:{key_id:s,parent_id:a||"root"},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(n){if(n.success&&n.data&&n.data.length>0){const i=r===0?o.rootVariantsLabel:`${o.selectLevel} ${r+1}`,l=`
                    <div class="variant-level mb-3" data-level="${r}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${i} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${r}">
                                    <option value="">Select ${i}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;t.append(l);const c=t.find(`[data-level="${r}"]`).find(".variant-level-select");let g='<option value="">Select variant</option>';n.data.forEach(d=>{const u=d.has_children?" 🌳":"";g+=`<option value="${d.id}" data-has-children="${d.has_children}">${d.name}${u}</option>`}),c.html(g),c.select2({theme:"bootstrap-5",width:"100%"}),n.data.length===1&&!n.data[0].has_children&&c.val(n.data[0].id).trigger("change"),x(e)}else r===0&&e.find(".variant-selection-info").show().find(".selection-text").text("No variants available for this key")},error:function(){console.error("Error loading variant level",r)}})}function z(e,s,a,r){const t=e.find(".nested-variant-levels"),o=e.find(".variant-key-select").val();t.find("[data-level]").each(function(){parseInt($(this).data("level"))>s&&$(this).remove()}),a&&r?A(e,o,a,s+1):a&&H(e,a),x(e)}function H(e,s){e.find(".final-variant-id").val(s),G(e)}function x(e){const s=window.productFormConfig,a=e.find(".variant-selection-info"),r=a.find(".selection-text"),t=e.find(".final-variant-id").val(),o=e.find(".variant-product-details"),n=e.find(".variant-details-path"),i=e.find(".variant-path-text");if(t){const l=[];if(e.find(".variant-level-select").each(function(){const c=$(this).find("option:selected");c.val()&&l.push(c.text().replace(" 🌳",""))}),l.length>0){const c=l.join(" - ");r.html(`<strong>${s.selectedColon}</strong> ${l.join(" → ")}`),a.removeClass("alert-info").addClass("alert-success").show(),i.text(c),n.show()}}else r.text(s.pleaseSelectVariant),a.removeClass("alert-success").addClass("alert-info").show(),n.hide(),o.hide()}function M(e){$(`.variant-box[data-variant-index="${e}"]`).find(".variant-key-select").select2({theme:"bootstrap-5",width:"100%"})}function G(e,s){e.find(".variant-product-details").show();const r=e.find(".variant-discount-toggle"),t=e.find(".variant-discount-fields");r.on("change",function(){$(this).is(":checked")?t.show():(t.hide(),t.find("input").val(""))})}function X(e){h&&h.length>0?w(e,h):(console.log("⏳ Regions not loaded yet for variant, waiting..."),setTimeout(function(){h&&h.length>0?w(e,h):(console.log("⚠️ Using fallback regions for variant"),U(e))},500))}function w(e,s){const a=$(`.variant-stock-rows[data-variant-index="${e}"]`),r=a.find("tr").length;let t='<option value="">Select Region</option>';s.forEach(function(i){const l=i.text||i.name;t+=`<option value="${i.id}">${l}</option>`});const o=`
        <tr class="variant-stock-row" data-variant-index="${e}" data-row-index="${r}">
            <td class="text-center">${r+1}</td>
            <td>
                <select name="variants[${e}][stock][${r}][region_id]" class="form-control region-select" required>
                    ${t}
                </select>
            </td>
            <td>
                <input type="number" name="variants[${e}][stock][${r}][quantity]"
                       class="form-control variant-stock-quantity" min="0" value="0" required>
            </td>
            <td class="actions">
                <button type="button" class="btn btn-sm btn-danger remove-variant-stock-row m-0" data-variant-index="${e}">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;a.append(o),a.find("tr").last().find(".region-select").select2({theme:"bootstrap-5",width:"100%"}),$(`.variant-stock-table-container[data-variant-index="${e}"]`).show(),$(`.variant-stock-empty-state[data-variant-index="${e}"]`).hide(),k(e)}function U(e){w(e,[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"}])}function k(e){const s=$(`.variant-stock-rows[data-variant-index="${e}"]`),a=$(`.variant-total-stock[data-variant-index="${e}"]`);let r=0;s.find(".variant-stock-quantity").each(function(){const t=parseInt($(this).val())||0;r+=t}),a.text(r)}function q(){$(".variant-box").length>0?($("#variants-empty-state").hide(),$("#variants-container").show()):($("#variants-empty-state").show(),$("#variants-container").hide())}function W(){$(".variant-box").each(function(e){const s=e+1;$(this).find("h6").html(`<i class="uil uil-cube"></i> Variant ${s}`)})}
