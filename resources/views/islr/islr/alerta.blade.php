<!-- Modal -->
<div class="modal fade" id="exampleModal{{$valorxml->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       ¿Confirma que desea eliminar el registro seleccionado?
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <a href="{{route('xml.delete',$valorxml->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
        
      </div>
    </div>
  </div>
</div>	<!--fin modal-->