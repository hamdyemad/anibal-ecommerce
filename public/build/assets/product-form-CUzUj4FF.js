let p=1;const b=4;let h={},u=0,m=null;document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".wizard-step-content").forEach(function(i,a){a===0?i.classList.add("active"):i.classList.remove("active")})});jQuery(document).ready(function(e){console.log("✅ Product form jQuery ready"),setTimeout(function(){e("#brand_id, #vendor_id, #department_id, #category_id, #sub_category_id, #tax_id, #configuration_type").each(function(){var t=e(this).find('option[value=""]').text().trim();console.log("📋 Select2 Init - ID:",e(this).attr("id"),"Placeholder:",t),e(this).select2({theme:"bootstrap-5",width:"100%",allowClear:!1,placeholder:t||"Select An Option"})})},100),e("#vendor_id").on("change",function(){var n;const t=e(this).val(),o=((n=window.productFormConfig)==null?void 0:n.vendorActivitiesMap)||{},l=o[t]||[];console.log("🔄 Vendor changed to:",t),console.log("📊 Vendor Activities Map:",o),console.log("📋 Vendor Activities:",l),e("#department_id option").each(function(){const s=e(this);if(s.val()===""){s.show();return}try{let d=s.attr("data-activities"),c=[];if(d){const f=document.createElement("textarea");f.innerHTML=d;const F=f.value;c=JSON.parse(F)}c.some(f=>l.includes(f))||l.length===0?s.show():(s.hide(),s.is(":selected")&&e("#department_id").val("").trigger("change"))}catch(d){console.error("Error parsing department activities:",d),s.show()}}),e("#department_id").select2("destroy").select2({theme:"bootstrap-5",width:"100%",allowClear:!1})}),setTimeout(function(){T()},500),window.testErrorContainers=function(){console.log("🧪 Testing error containers..."),[1,2].forEach(o=>{const l=`error-translations-${o}-title`,n=e(`#${l}`);console.log(`📝 Container #${l}: exists=${n.length>0}`),n.length>0&&(n.html('<i class="uil uil-exclamation-triangle"></i> Test error message').show(),console.log(`✅ Test error displayed in ${l}`),setTimeout(()=>{n.hide().empty(),console.log(`🧹 Cleared test error from ${l}`)},3e3))}),console.log("💡 Check the form to see test error messages appear and disappear")},window.testTitleError=function(){console.log("🧪 Testing Title (English) error specifically...");const t=e('input[name="translations[1][title]"]'),o=e("#error-translations-1-title");console.log("📝 Title input found:",t.length>0),console.log("📝 Title input element:",t[0]),console.log("📝 Error container found:",o.length>0),console.log("📝 Error container element:",o[0]),o.length>0?(console.log("📝 Container current display:",o.css("display")),console.log("📝 Container current visibility:",o.is(":visible")),console.log("📝 Container classes:",o.attr("class")),console.log("📝 Container style attribute:",o.attr("style")),o.html('<i class="uil uil-exclamation-triangle"></i> TEST: Title is required for English'),o.show(),o.css("display","block"),o.css("visibility","visible"),o.removeClass("d-none").addClass("d-block"),o.attr("style","display: block !important;"),console.log("📝 After force show - display:",o.css("display")),console.log("📝 After force show - visible:",o.is(":visible")),console.log("📝 After force show - style:",o.attr("style")),t.addClass("is-invalid"),console.log("✅ Test error should now be visible under Title (English) field")):console.log("❌ Error container not found for Title (English)")},window.testErrorContainer=function(){console.log("🧪 Direct test of error-translations-1-title container...");const t=e("#error-translations-1-title");console.log("📝 Container found:",t.length>0),console.log("📝 Container element:",t[0]),t.length>0?(console.log("📝 Current display:",t.css("display")),console.log("📝 Current visibility:",t.css("visibility")),console.log("📝 Is visible:",t.is(":visible")),console.log("📝 Current content:",t.html()),console.log("📝 Current classes:",t.attr("class")),console.log("📝 Current style:",t.attr("style")),t.html('<i class="uil uil-exclamation-triangle"></i> DIRECT TEST MESSAGE'),t.show(),t.css("display","block"),t.css("visibility","visible"),t.removeClass("d-none").addClass("d-block"),t.attr("style","display: block !important; visibility: visible !important;"),console.log("📝 After force show:"),console.log("📝 Display:",t.css("display")),console.log("📝 Visibility:",t.css("visibility")),console.log("📝 Is visible:",t.is(":visible")),console.log("📝 Style attr:",t.attr("style")),console.log("✅ Direct test completed - check if message appears")):console.log("❌ Container #error-translations-1-title not found")};function i(){console.log("🔧 Attaching event handlers to Select2 dropdowns..."),e("#vendor_id");const t=e("#department_id"),o=window.productFormConfig.vendorActivitiesMap||{};Object.keys(o).length>0&&(console.log("👤 Admin/Super Admin user detected - hiding departments until vendor selected"),t.find("option[value!=''][data-activities]").hide()),e(document).off("change.productForm","#vendor_id").on("change.productForm","#vendor_id",function(n){console.log("🎯 Vendor changed");const s=e(this).val(),r=e("#department_id");r.val();const d=`${window.productFormConfig.departmentsRoute}?vendor_id=${s}&select2=1`;console.log("🔍 Vendor ID:",s),fetch(d,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(c=>{if(console.log("📥 Departments response status:",c.status),!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{console.log("✅ Departments API response:",c),r.empty().append('<option value="">Select Department</option>').prop("disabled",!1),c.status&&c.data&&c.data.length>0?(c.data.forEach(g=>{r.append(`<option value="${g.id}">${g.name}</option>`)}),console.log(`✅ Loaded ${c.data.length} departments`)):(console.log("⚠️ No departments found for vendor:",s),r.append('<option value="">No departments available</option>')),r.trigger("change")}).catch(c=>{console.error("❌ Error loading departments:",c),r.empty().append('<option value="">Error loading departments</option>').prop("disabled",!1).trigger("change")})}),e(document).off("change.productForm","#department_id").on("change.productForm","#department_id",function(n){console.log("🎯 Department event triggered:",n.type);const s=e(this).val();console.log("🔄 Department changed:",s);const r=e("#category_id"),d=e("#sub_category_id");if(r.empty().append('<option value="">Loading categories...</option>').prop("disabled",!0).trigger("change"),d.empty().append('<option value="">Select Sub Category</option>').val("").trigger("change"),s){const c=`${window.productFormConfig.categoriesRoute}?department_id=${s}&select2=1`;console.log("🌐 Fetching categories from:",c),fetch(c,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(g=>{if(console.log("📥 Categories response status:",g.status),!g.ok)throw new Error(`HTTP error! status: ${g.status}`);return g.json()}).then(g=>{console.log("✅ Categories API response:",g),r.empty().append('<option value="">Select Category</option>').prop("disabled",!1),g.status&&g.data&&g.data.length>0?(g.data.forEach(f=>{r.append(`<option value="${f.id}">${f.name}</option>`)}),console.log(`✅ Loaded ${g.data.length} categories`)):(console.log("⚠️ No categories found for department:",s),r.append('<option value="">No categories available</option>')),r.trigger("change")}).catch(g=>{console.error("❌ Error loading categories:",g),r.empty().append('<option value="">Error loading categories</option>').prop("disabled",!1).trigger("change")})}else r.empty().append('<option value="">Select Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Department handler attached"),e("#category_id").off("change.productForm select2:select.productForm"),e(document).off("change.productForm","#category_id").on("change.productForm","#category_id",function(n){console.log("🎯 Category event triggered:",n.type);const s=e(this).val();console.log("🔄 Category changed:",s);const r=e("#sub_category_id");if(r.empty().append('<option value="">Loading subcategories...</option>').prop("disabled",!0).trigger("change"),s){const d=`${window.productFormConfig.subCategoriesRoute}?category_id=${s}`;console.log("🌐 Fetching subcategories from:",d),fetch(d,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(c=>{if(console.log("📥 SubCategories response status:",c.status),!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{console.log("✅ SubCategories API response:",c),r.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1),c.status&&c.data&&c.data.length>0?(c.data.forEach(g=>{r.append(`<option value="${g.id}">${g.name}</option>`)}),console.log(`✅ Loaded ${c.data.length} subcategories`)):(console.log("⚠️ No subcategories found for category:",s),r.append('<option value="">No subcategories available</option>')),r.trigger("change")}).catch(c=>{console.error("❌ Error loading subcategories:",c),r.empty().append('<option value="">Error loading subcategories</option>').prop("disabled",!1).trigger("change")})}else r.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Category handler attached"),console.log("✅ All handlers ready!")}function a(){const t=e("#department_id");t.length&&t.hasClass("select2-hidden-accessible")?i():setTimeout(a,100)}setTimeout(a,200),e("#productForm").on("submit",S),e(".additional-image-item").each(function(){const t=e(this).data("image-id");t&&D(`existing_image_${t}`)}),e("#add-additional-image-btn").on("click",function(t){console.log("🖱️ Add Image button clicked!"),t.preventDefault(),console.log("🎯 Calling addAdditionalImage function..."),Q()}),e(document).on("click",".remove-additional-image-btn",function(t){t.preventDefault();const o=e(this).closest(".additional-image-item"),l=o.data("image-id");if(l){const n=e(`<input type="hidden" name="deleted_images[]" value="${l}">`);e("#productForm").append(n)}o.remove(),R()}),v(p),e("#nextBtn").on("click",function(){console.log("📍 Next button clicked. Current step:",p),p++,p>b&&(p=b),v(p)}),e("#prevBtn").on("click",function(){p--,p<1&&(p=1),v(p)}),e(".wizard-step-nav").on("click",function(){console.log("🖱️ Wizard step clicked!");const t=parseInt(e(this).data("step"));console.log("Clicked step:",t),e("#validation-alerts-container").hide().empty(),p=t,v(p)}),e(document).on("click",".edit-step",function(){p=parseInt(e(this).data("step")),v(p),e("html, body").animate({scrollTop:e(".card").offset().top-100},300)}),e("#productForm").on("submit",S),e("#productForm").on("input keyup change","input, textarea, select",function(){const t=e(this),o=t.attr("name");if(o){t.removeClass("is-invalid");let l=null;if(o.includes("translations[")){const n=o.match(/translations\[(\d+)\]\[([^\]]+)\]/);if(n){const s=n[1],r=n[2];l=e(`#error-translations-${s}-${r}`)}}if(!l||!l.length){const n=[`#error-${o}`,`#error-${o.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`,`[data-error-for="${o}"]`];for(const s of n)if(l=e(s),l.length>0)break}l&&l.length&&l.hide().empty(),t.closest(".form-group").find(".error-message:not([id])").remove(),t.siblings(".error-message:not([id])").remove(),(t.hasClass("select2")||t.data("select2"))&&t.next(".select2-container").siblings(".error-message:not([id])").remove(),console.log(`🧹 Cleared error for field: ${o}`)}}),e("#configuration_type").on("change",function(){const t=e(this).val();t==="simple"?(e("#simple-product-section").show(),e("#variants-section").hide()):t==="variants"?(e("#simple-product-section").hide(),e("#variants-section").show()):(e("#simple-product-section").hide(),e("#variants-section").hide())}),e("#has_discount").on("change",function(){e(this).is(":checked")?e("#discount-fields").slideDown():(e("#discount-fields").slideUp(),e("#price_before_discount").val(""),e("#offer_end_date").val(""))}),e("#add-stock-row").on("click",function(){P()}),e(document).on("click",".remove-stock-row",function(){e(this).closest("tr").remove(),y(),_(),A()}),e(document).on("input",".stock-quantity",function(){y()}),e("#add-variant-btn").on("click",function(){M()}),e(document).on("click",".remove-variant-btn",function(){e(this).closest(".variant-box").remove(),L(),K()}),e(document).on("change",".variant-key-select",function(){const t=e(this).closest(".variant-box"),o=e(this).val();o?(t.find(".variant-tree-container").show(),H(t,o)):(t.find(".variant-tree-container").hide(),t.find(".final-variant-id").val(""))}),e(document).on("change",".variant-level-select",function(){const t=e(this).closest(".variant-box"),o=parseInt(e(this).data("level")),l=e(this).val(),n=e(this).find("option:selected").data("has-children");t.find(".final-variant-id").val(""),l?z(t,o,l,n):(t.find(".nested-variant-levels").find("[data-level]").each(function(){parseInt(e(this).data("level"))>o&&e(this).remove()}),x(t))}),e(document).on("click",".add-stock-row-variant",function(){const t=e(this).data("variant-index");B(t)}),e(document).on("click",".remove-variant-stock-row",function(){const t=e(this).closest("tr"),o=e(this).data("variant-index"),l=e(`.variant-stock-rows[data-variant-index="${o}"]`);t.remove(),l.find("tr").each(function(n){e(this).find("td:first").text(n+1)}),l.find("tr").length===0&&(e(`.variant-stock-table-container[data-variant-index="${o}"]`).hide(),e(`.variant-stock-empty-state[data-variant-index="${o}"]`).show()),k(o)}),e(document).on("input",".variant-stock-quantity",function(){const o=e(this).closest("tr").data("variant-index");k(o)}),q(),typeof LoadingOverlay<"u"&&LoadingOverlay.init?(console.log("🔄 Initializing LoadingOverlay..."),LoadingOverlay.init(),console.log("✅ LoadingOverlay initialized")):console.warn("⚠️ LoadingOverlay not available"),console.log("✅ Product form navigation initialized")});function q(){console.log("🌍 Loading regions data..."),$.ajax({url:"/api/area/regions?select2=1",method:"GET",dataType:"json",success:function(i){m=i.data,console.log("✅ Regions loaded successfully:",m)},error:function(i,a,t){console.log("❌ API error, using fallback regions"),m=[{id:1,text:"Cairo",name:"Cairo"},{id:2,text:"Alexandria",name:"Alexandria"},{id:3,text:"Giza",name:"Giza"},{id:4,text:"Luxor",name:"Luxor"},{id:5,text:"Aswan",name:"Aswan"}],console.log("✅ Fallback regions set:",m)}})}function v(e){$(".wizard-step-content").each(function(){$(this).removeClass("active").css("display","none")});const i=$(`.wizard-step-content[data-step="${e}"]`);if(i.length&&i.addClass("active").css("display","block"),Object.keys(h).length>0&&e!==4)for(let a in h){const t=E(a),o=i.find(`[name="${t}"], [name="${t}[]"], [name="${a}"], [name="${a}[]"]`).first();if(o.length){o.addClass("is-invalid");let l=null;if(a.includes("translations.")){const n=a.split(".");if(n.length===3&&n[0]==="translations"){const s=n[1],r=n[2],d=`error-translations-${s}-${r}`;l=$(`#${d}`)}}if(!l||!l.length){const n=o.attr("name"),s=[`#error-${a}`,`#error-${n}`,`#error-${a.replace(/\./g,"-")}`,`#error-${n.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];for(const r of s)if(l=$(r),l.length>0)break}if(l&&l.length){const n=`<i class="uil uil-exclamation-triangle"></i> ${h[a][0]}`;l.html(n).show().css("display","block").removeClass("d-none").addClass("d-block")}else{o.closest(".form-group").find(".error-message:not([id])").remove();const n=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${h[a][0]}</div>`;if(o.hasClass("select2")||o.data("select2")){const s=o.next(".select2-container");s.length?s.after(n):o.after(n)}else o.after(n)}}}$(".wizard-step-nav").removeClass("current"),$(`.wizard-step-nav[data-step="${e}"]`).addClass("current"),$(".wizard-step-nav").each(function(){parseInt($(this).data("step"))<e?$(this).addClass("completed"):$(this).removeClass("completed")}),e===1?$("#prevBtn").hide():$("#prevBtn").show(),e===b?($("#nextBtn").hide(),$("#submitBtn").show()):($("#nextBtn").show(),$("#submitBtn").hide()),$("html, body").animate({scrollTop:$(".card-body").offset().top-100},300)}function E(e){const i=e.split(".");if(i.length===1)return e;let a=i[0];for(let t=1;t<i.length;t++)a+=`[${i[t]}]`;return a}function T(){console.log("🔧 Ensuring all form fields have error containers..."),$("#productForm").find("input, select, textarea").each(function(){const e=$(this),i=e.attr("name");if(!i)return;const a=`error-${i.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`;if($(`#${a}`).length===0){const t=`<div class="error-message text-danger" id="${a}" style="display: none;"></div>`;if(e.hasClass("select2")||e.data("select2")){const o=e.next(".select2-container");o.length?o.after(t):e.after(t)}else e.after(t);console.log(`✅ Created error container for: ${i}`)}})}function V(e){h=e,T();let i='<ul class="mb-0">';for(let a in e){const t=e[a];t.forEach(n=>{i+=`<li class="mb-2">${n}</li>`});const o=E(a),l=$(`[name="${o}"], [name="${o}[]"], [name="${a}"], [name="${a}[]"]`).first();if(console.log(`🔍 Looking for field: ${a} -> ${o}, found: ${l.length>0}`),a.includes("translations.")&&a.includes(".title")){console.log(`📝 Translation title field detected: ${a}`);const n=`error-translations-${a.split(".")[1]}-title`;console.log(`📝 Expected error container ID: ${n}`),console.log("📝 Container exists:",$(`#${n}`).length>0),console.log("📝 Container element:",$(`#${n}`)[0])}if(l.length){l.addClass("is-invalid");let n=null;if(a.includes("translations.")){const s=a.split(".");if(s.length===3&&s[0]==="translations"){const r=s[1],d=s[2],c=`error-translations-${r}-${d}`;n=$(`#${c}`),console.log(`🔍 Looking for translation container: #${c}, found: ${n.length>0}`),n.length>0&&console.log("📝 Container element:",n[0])}}if(!n||!n.length){const s=l.attr("name"),r=[`#error-${a}`,`#error-${s}`,`#error-${a.replace(/\./g,"-")}`,`#error-${s.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];console.log("🔍 Trying fallback selectors:",r);for(const d of r)if(n=$(d),n.length>0){console.log(`✅ Found with selector: ${d}`);break}}if(n&&n.length){const s=`<i class="uil uil-exclamation-triangle"></i> ${t[0]}`;console.log(`✅ Using existing container for ${a}, setting content: ${s}`),n.html(s),n.show(),n.css("display","block"),n.css("visibility","visible"),n.removeClass("d-none").addClass("d-block"),n.attr("style","display: block !important;"),console.log(`✅ Container after update - visible: ${n.is(":visible")}, display: ${n.css("display")}`)}else{const s=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${t[0]}</div>`;if(l.closest(".form-group").find(".error-message:not([id])").remove(),l.hasClass("select2")||l.data("select2")){const r=l.next(".select2-container");r.length?r.after(s):l.after(s)}else l.after(s);console.log(`✅ Created new error message for field: ${a}`)}}else console.log(`❌ Field element not found for: ${a} (${o})`)}if(i+="</ul>",Object.keys(e).length>0){const t=`
            <div class="alert alert-danger alert-dismissible fade show validation-errors-alert" role="alert">
                <div class="d-flex align-items-start">
                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 18px; margin-top: 2px;"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">${document.documentElement.dir==="rtl"||document.documentElement.lang==="ar"||$("html").attr("lang")==="ar"?"يرجى تصحيح الأخطاء التالية:":"Please correct the following errors:"}</h6>
                        ${i}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;$(".validation-errors-alert").remove();const o=$("#validation-alerts-container");o.length?(console.log("✅ Adding alert to validation-alerts-container"),o.html(t).show()):(console.log("⚠️ validation-alerts-container not found, using fallback"),$(".card-body").prepend(t)),setTimeout(()=>{const l=$(".validation-errors-alert");l.length?(l.show(),console.log("✅ Alert should now be visible"),$("html, body").animate({scrollTop:l.offset().top-100},300)):console.log("❌ Alert element not found after creation")},100)}}function O(){$(".wizard-step-content:not(.active)").each(function(){$(this).find("[required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}),$("#simple-product-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")}),$("#variants-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}function C(){$('[data-was-required="true"]').each(function(){$(this).attr("required","required").removeAttr("data-was-required")})}function S(e){console.log("Form submission started"),e.preventDefault();const i=window.productFormConfig;if(!i){console.error("productFormConfig is not defined");return}if(O(),typeof LoadingOverlay<"u")LoadingOverlay.overlay||(console.log("Initializing LoadingOverlay..."),LoadingOverlay.init());else{console.error("LoadingOverlay is not defined");return}const a=new FormData(this),t=$(this).attr("action"),l=$('input[name="_method"][value="PUT"]').length>0?i.updatingProduct||"Updating product...":i.creatingProduct||"Creating product...",n=document.getElementById("loadingOverlay");n&&(n.querySelector(".loading-text").textContent=l,n.querySelector(".loading-subtext").textContent=i.pleaseWait||"Please wait..."),LoadingOverlay.show(),LoadingOverlay.animateProgressBar(30,300).then(()=>fetch(t,{method:"POST",body:a,headers:{"X-Requested-With":"XMLHttpRequest",Accept:"application/json"}})).then(s=>(LoadingOverlay.animateProgressBar(60,200),s.ok?s.json():s.json().then(r=>{throw r}))).then(s=>LoadingOverlay.animateProgressBar(90,200).then(()=>s)).then(s=>LoadingOverlay.animateProgressBar(100,200).then(()=>{C();const r=$('input[name="_method"][value="PUT"]').length>0,d=s.message||(r?i.productUpdated:i.productCreated)||"Product saved successfully!";LoadingOverlay.showSuccess(d,i.redirecting||"Redirecting..."),setTimeout(()=>{window.location.href=s.redirect||i.indexRoute||"/admin/products"},1500)})).catch(s=>{if(LoadingOverlay.hide(),C(),console.log("Error:",s),s.errors)console.log("Validation errors:",s.errors),V(s.errors);else{const r=s.message||"An error occurred. Please try again.";console.error("Error message:",r),alert(r)}})}function P(){j(m)}function j(e){const i=$(".stock-row").length;let a='<option value="">Select Region</option>';e.forEach(l=>{const n=l.text||l.name;a+=`<option value="${l.id}">${n}</option>`});const o=`
        <tr class="stock-row">
            <td>${i+1}</td>
            <td>
                <select name="stocks[${i}][region_id]" class="form-control select2-stock" required>
                    ${a}
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
    `;$("#stock-rows").append(o),_(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),y(),A()}function A(){$(".stock-row").each(function(e){$(this).find("td:first").text(e+1)})}function _(){$(".stock-row").length>0?($("#stock-table-container").show(),$("#stock-empty-state").hide()):($("#stock-table-container").hide(),$("#stock-empty-state").show())}function y(){let e=0;$(".stock-quantity").each(function(){const i=parseInt($(this).val())||0;e+=i}),$("#total-stock").text(e)}function M(){u++;const e=window.productFormConfig,i=`
        <div class="variant-box card mb-3" data-variant-index="${u}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            ${e.variantNumber} ${u}
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
                        <select name="variants[${u}][key_id]" class="form-control variant-key-select" required>
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
                    <input type="hidden" name="variants[${u}][value_id]" class="final-variant-id">

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
                                        <input type="text" name="variants[${u}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${e.price} <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${u}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">${e.enableDiscountOffer}</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${u}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${e.priceBeforeDiscount}</label>
                                                <input type="number" name="variants[${u}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${e.offerEndDate}</label>
                                                <input type="date" name="variants[${u}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
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
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${u}">
                                    <i class="uil uil-plus"></i> ${e.addNewRegion}
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${u}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">${e.noRegionsAddedYet}</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${u}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${u}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">${e.region}</span></th>
                                                    <th><span class="userDatatable-title">${e.stockQuantity}</span></th>
                                                    <th><span class="userDatatable-title">${e.actionsLabel}</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${u}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">${e.totalStock}:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${u}">0</span>
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
    `;$("#variants-container").append(i),L(),N(u),G(u)}function N(e){const a=$(`.variant-box[data-variant-index="${e}"]`).find(".variant-key-select");$.ajax({url:"/admin/api/variant-keys",method:"GET",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(t){if(t.success&&t.data){let o='<option value="">Select Variant Key</option>';t.data.forEach(l=>{o+=`<option value="${l.id}">${l.name}</option>`}),a.html(o)}else a.html('<option value="">Error loading keys</option>')},error:function(){a.html('<option value="">Error loading keys</option>')}})}function H(e,i){const a=e.find(".nested-variant-levels"),t=e.find(".variant-selection-info");if(!i){a.empty(),t.hide();return}a.empty(),t.show().find(".selection-text").text("Loading variants..."),I(e,i,null,0)}function I(e,i,a,t){const o=e.find(".nested-variant-levels"),l=window.productFormConfig;$.ajax({url:"/admin/api/variants-by-key",method:"GET",data:{key_id:i,parent_id:a||"root"},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(n){if(n.success&&n.data&&n.data.length>0){const s=t===0?l.rootVariantsLabel:`${l.selectLevel} ${t+1}`,r=`
                    <div class="variant-level mb-3" data-level="${t}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${s} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${t}">
                                    <option value="">Select ${s}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;o.append(r);const d=o.find(`[data-level="${t}"]`).find(".variant-level-select");let c='<option value="">Select variant</option>';n.data.forEach(g=>{const f=g.has_children?" 🌳":"";c+=`<option value="${g.id}" data-has-children="${g.has_children}">${g.name}${f}</option>`}),d.html(c),d.select2({theme:"bootstrap-5",width:"100%"}),n.data.length===1&&!n.data[0].has_children&&d.val(n.data[0].id).trigger("change"),x(e)}else t===0&&e.find(".variant-selection-info").show().find(".selection-text").text("No variants available for this key")},error:function(){console.error("Error loading variant level",t)}})}function z(e,i,a,t){const o=e.find(".nested-variant-levels"),l=e.find(".variant-key-select").val();o.find("[data-level]").each(function(){parseInt($(this).data("level"))>i&&$(this).remove()}),a&&t?I(e,l,a,i+1):a&&U(e,a),x(e)}function U(e,i){e.find(".final-variant-id").val(i),X(e)}function x(e){const i=window.productFormConfig,a=e.find(".variant-selection-info"),t=a.find(".selection-text"),o=e.find(".final-variant-id").val(),l=e.find(".variant-product-details"),n=e.find(".variant-details-path"),s=e.find(".variant-path-text");if(o){const r=[];if(e.find(".variant-level-select").each(function(){const d=$(this).find("option:selected");d.val()&&r.push(d.text().replace(" 🌳",""))}),r.length>0){const d=r.join(" - ");t.html(`<strong>${i.selectedColon}</strong> ${r.join(" → ")}`),a.removeClass("alert-info").addClass("alert-success").show(),s.text(d),n.show()}}else t.text(i.pleaseSelectVariant),a.removeClass("alert-success").addClass("alert-info").show(),n.hide(),l.hide()}function G(e){$(`.variant-box[data-variant-index="${e}"]`).find(".variant-key-select").select2({theme:"bootstrap-5",width:"100%"})}function X(e,i){e.find(".variant-product-details").show();const t=e.find(".variant-discount-toggle"),o=e.find(".variant-discount-fields");t.on("change",function(){$(this).is(":checked")?o.show():(o.hide(),o.find("input").val(""))})}function B(e){m&&m.length>0?w(e,m):(console.log("⏳ Regions not loaded yet for variant, waiting..."),setTimeout(function(){m&&m.length>0?w(e,m):(console.log("⚠️ Using fallback regions for variant"),W(e))},500))}function w(e,i){const a=$(`.variant-stock-rows[data-variant-index="${e}"]`),t=a.find("tr").length;let o='<option value="">Select Region</option>';i.forEach(function(s){o+=`<option value="${s.id}">${s.text}</option>`});const l=`
        <tr class="variant-stock-row" data-variant-index="${e}" data-row-index="${t}">
            <td class="text-center">${t+1}</td>
            <td>
                <select name="variants[${e}][stock][${t}][region_id]" class="form-control region-select" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="variants[${e}][stock][${t}][quantity]"
                       class="form-control variant-stock-quantity" min="0" value="0" required>
            </td>
            <td class="actions">
                <button type="button" class="btn btn-sm btn-danger remove-variant-stock-row m-0" data-variant-index="${e}">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;a.append(l),a.find("tr").last().find(".region-select").select2({theme:"bootstrap-5",width:"100%"}),$(`.variant-stock-table-container[data-variant-index="${e}"]`).show(),$(`.variant-stock-empty-state[data-variant-index="${e}"]`).hide(),k(e)}function W(e){w(e,[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"}])}function k(e){const i=$(`.variant-stock-rows[data-variant-index="${e}"]`),a=$(`.variant-total-stock[data-variant-index="${e}"]`);let t=0;i.find(".variant-stock-quantity").each(function(){const o=parseInt($(this).val())||0;t+=o}),a.text(t)}function L(){$(".variant-box").length>0?($("#variants-empty-state").hide(),$("#variants-container").show()):($("#variants-empty-state").show(),$("#variants-container").hide())}function K(){$(".variant-box").each(function(e){const i=e+1;$(this).find("h6").html(`<i class="uil uil-cube"></i> Variant ${i}`)})}function Q(){console.log("🖼️ Adding new additional image...");const e=window.productFormConfig,i=$("#additional-images-container");console.log("📦 Container found:",i.length>0),console.log("📦 Container display:",i.css("display"));const a=i.find(".additional-image-item").length+1,t="new_image_"+Date.now()+"_"+Math.random().toString(36).substr(2,9);console.log("🆔 Unique ID:",t),console.log("📊 Image count:",a);const o=`
        <div class="col-md-6 col-lg-4 mb-3 additional-image-item" data-index="${a}">
            <div class="form-group">
                <label class="il-gray fs-14 fw-500 mb-10">
                    ${e.additionalImage} ${a}
                </label>
                <div class="image-upload-wrapper">
                    <div class="image-preview-container" id="${t}-preview-container" data-target="${t}">
                        <div class="image-placeholder" id="${t}-placeholder">
                            <i class="uil uil-image-plus"></i>
                            <p>${e.clickToUploadImage}</p>
                            <small>${e.recommendedSize}</small>
                        </div>
                        <div class="image-overlay">
                            <button type="button" class="btn-change-image" data-target="${t}">
                                <i class="uil uil-camera"></i> ${e.change}
                            </button>
                            <button type="button" class="btn-remove-image remove-additional-image-btn" data-target="${t}" style="display: none;">
                                <i class="uil uil-trash-alt"></i> ${e.remove}
                            </button>
                        </div>
                    </div>
                    <input type="file"
                           class="d-none image-file-input"
                           id="${t}"
                           name="additional_images[]"
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           data-preview="${t}">
                </div>
            </div>
        </div>
    `;console.log("📝 Appending image HTML to container..."),i.append(o),console.log("✅ Image HTML appended"),console.log("📦 Container now has",i.find(".additional-image-item").length,"items"),D(t),R(),console.log("✅ Additional image added successfully")}function D(e){const i=$(`#${e}`),a=$(`#${e}-preview-container`),t=$(`#${e}-placeholder`),o=a.find(".btn-change-image"),l=a.find(".btn-remove-image");a.on("click",function(n){!$(n.target).closest(".btn-change-image")&&!$(n.target).closest(".btn-remove-image")&&i.click()}),o.on("click",function(n){n.stopPropagation(),n.preventDefault(),i.click()}),i.on("change",function(n){const s=n.target.files[0];if(s){const r=new FileReader;r.onload=function(d){let c=$(`#${e}-preview-img`);if(c.length===0){const g=$(`<img id="${e}-preview-img" class="preview-image" src="${d.target.result}">`);a.prepend(g)}else c.attr("src",d.target.result);t.hide(),l.show()},r.readAsDataURL(s)}}),l.on("click",function(n){n.stopPropagation(),i.val("");const s=$(`#${e}-preview-img`);s.length>0&&s.remove(),t.show(),l.hide()})}function R(){const e=$("#additional-images-container"),i=$("#additional-images-empty-state");e.find(".additional-image-item").length>0?(e.show(),i.hide()):(e.hide(),i.show())}
