function removeParentTr(id){
    $('#'+id).parents("tr").eq(0).remove();
}