<?php
class M_user extends CI_Model{

  public function update_password($tabel,$where,$data)
  {
    $this->db->where($where);
    $this->db->update($tabel,$data);
  }

  public function select($tabel)
  {
    return $this->db->select()
                    ->from($tabel)
                    ->get()->result();
  }
}

 ?>
