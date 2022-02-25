import { Component, OnInit } from '@angular/core';
import { NgModule } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Etapa } from '../etapa';
import { EnviarService } from '../enviar.service';
@Component({
  selector: 'app-alta-etapa',
  templateUrl: './alta-etapa.component.html',
  styleUrls: ['./alta-etapa.component.css']
})
export class AltaEtapaComponent implements OnInit {
  formulario:any
  etapa:Etapa
  respuesta :any
  r:any
  constructor(private enviar:EnviarService) {
    this.formulario=null
    this.etapa=new Etapa('','','')
    this.respuesta = []
    this.r = []
  }
  ngOnInit(): void {
    this.enviar.recibir().subscribe(res =>{
      this.r = res
      console.log(this.r)
      this.respuesta = JSON.parse(this.r)
    } )



    this.formulario = new FormGroup({
      idEtapa: new FormControl('',[Validators.required,Validators.minLength(1),Validators.maxLength(2)]),
      duracion: new FormControl('',[Validators.required,Validators.pattern(/^[0-9]?[0-9]?[0-9]:[0-5][0-9]$/gm)]),
      longitud: new FormControl('',[Validators.required,Validators.pattern(/^\d{1,4}(\,\d{1,3})?[ ]?$/gm)]),
      img: new FormControl(''),
      select: new FormControl('')
    });
  }
  get idEtapa() { return this.formulario.get('idEtapa'); }
  get duracion() { return this.formulario.get('duracion'); }
  get longitud() { return this.formulario.get('longitud'); }
  get img() { return this.formulario.get('img'); }
  get select() { return this.formulario.get('select'); }
  anyadir(){
    console.log("componente1.enviar()")
    let datos:any = []
    datos[0]=this.formulario.get('idEtapa').value
    datos[1]=this.formulario.get('duracion').value
    datos[2]=this.formulario.get('longitud').value
    //En lugar de la función flecha, llamar a un método del componente.
    console.log(datos)

    this.enviar.enviar(datos).subscribe(res => console.log(res))

  }

}
