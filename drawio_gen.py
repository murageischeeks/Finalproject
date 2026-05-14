import xml.etree.ElementTree as ET

def generate_drawio_xml():
    root = ET.Element("mxGraphModel", dx="1000", dy="1000", grid="1", gridSize="10", guides="1", tooltips="1", connect="1", arrows="1", fold="1", page="1", pageScale="1", pageWidth="1400", pageHeight="1000", math="0", shadow="0")
    root_node = ET.SubElement(root, "root")
    
    ET.SubElement(root_node, "mxCell", id="0")
    ET.SubElement(root_node, "mxCell", id="1", parent="0")
    
    lifelines = [
        ("Patient", "Patient", 40),
        ("Controller", "PatientFollowUpController", 220),
        ("Triage", "TriageClassificationService", 460),
        ("DB", "PostgreSQL DB", 640),
        ("Audit", "AuditLogService", 820),
        ("Queue", "ProcessFollowUpSubmission", 1000),
        ("API", "OpenMRS API", 1180),
    ]
    
    for obj_id, name, x in lifelines:
        cell = ET.SubElement(root_node, "mxCell", id=obj_id, value=name, style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;", vertex="1", parent="1")
        ET.SubElement(cell, "mxGeometry", x=str(x), y="40", width="120", height="750", **{"as": "geometry"})
        
    messages = [
        ("Patient", "Controller", "1. POST /patient/followup", False),
        ("Controller", "DB", "2. FollowUpSubmission::create()", False),
        ("DB", "Controller", "3. Return record", True),
        ("Controller", "Triage", "4. classify(submission)", False),
        ("Triage", "Controller", "5. Return urgencyLevel", True),
        ("Controller", "DB", "6. update urgency_level", False),
        ("Controller", "Audit", "7. log('submission_created')", False),
        ("Audit", "DB", "8. insert into audit_logs", False),
        ("Controller", "Queue", "9. dispatch(submission) to queue", False),
        ("Controller", "Patient", "10. 302 Redirect to Confirmation", True),
        ("Queue", "API", "11. POST /api/emr/observations", False),
        ("API", "Queue", "12. [Success] 201 Created", True),
        ("Queue", "DB", "13. update sync_status='Synced'", False),
        ("Queue", "Audit", "14. log('sync_complete')", False),
    ]
    
    y = 100
    msg_idx = 100
    for src, dst, text, dashed in messages:
        style = "html=1;verticalAlign=bottom;endArrow=block;edgeStyle=elbowEdgeStyle;elbow=vertical;curved=0;rounded=0;"
        if dashed:
            style += "dashed=1;"
            
        src_x = next(l[2] for l in lifelines if l[0] == src) + 60
        dst_x = next(l[2] for l in lifelines if l[0] == dst) + 60
        
        cell = ET.SubElement(root_node, "mxCell", id=str(msg_idx), value=text, style=style, edge="1", parent="1")
        geo = ET.SubElement(cell, "mxGeometry", width="80", relative="1", **{"as": "geometry"})
        
        # Source point
        ET.SubElement(geo, "mxPoint", x=str(src_x), y=str(y), **{"as": "sourcePoint"})
        # Target point
        ET.SubElement(geo, "mxPoint", x=str(dst_x), y=str(y), **{"as": "targetPoint"})
        
        y += 40
        msg_idx += 1
        
    # Generate the XML string
    import xml.dom.minidom
    xmlstr = xml.dom.minidom.parseString(ET.tostring(root)).toprettyxml(indent="  ")
    
    with open("drawio_sequence.xml", "w") as f:
        f.write(xmlstr)

if __name__ == "__main__":
    generate_drawio_xml()
