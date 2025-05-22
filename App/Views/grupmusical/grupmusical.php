<?php
// Obtenim els grups musicals des de sessió (carregats prèviament al controlador)
$grups = $_SESSION['grupos'] ?? [];
?>


<div class="container my-5">
   <h2 class="text-center mb-4">Grups Musicals</h2>


   <div id="grups-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"></div>
</div>


<script>
   const grups = JSON.parse(`<?= json_encode(array_values($grups)) ?>`);
   const container = document.getElementById('grups-container');


   if (grups.length === 0) {
       container.innerHTML = '<div class="col text-center">No hi ha grups musicals registrats.</div>';
   } else {
       grups.forEach(grup => {
           const col = document.createElement('div');
           col.className = 'col';
           col.innerHTML = `
               <div class="card shadow-sm h-100">
                   <div class="card-body d-flex flex-column">
                       <h5 class="card-title text-primary fw-bold">
                           <i class="bi bi-person-lines-fill"></i> ${grup.nomGrup}
                       </h5>
                       <p class="card-text mb-1"><i class="bi bi-calendar-event"></i> Fundat: ${grup.dataCreacio}</p>
                       <p class="card-text mb-1"><i class="bi bi-info-circle"></i> ${grup.descripcio || 'Sense descripció.'}</p>
                       <div class="mt-auto">
                           <a href="/grup/detalle/${grup.idGrup}" class="btn btn-primary w-100" style="background-color:#624DE3;">
                               Veure detalls
                           </a>
                       </div>
                   </div>
               </div>
           `;
           container.appendChild(col);
       });
   }
</script>






