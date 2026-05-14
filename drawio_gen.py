import xml.etree.ElementTree as ET
import xml.dom.minidom

def generate_drawio_xml():
    root = ET.Element("mxGraphModel", dx="1000", dy="1000", grid="1", gridSize="10", guides="1", tooltips="1", connect="1", arrows="1", fold="1", page="1", pageScale="1", pageWidth="1600", pageHeight="1200", math="0", shadow="0")
    root_node = ET.SubElement(root, "root")
    
    ET.SubElement(root_node, "mxCell", id="0")
    ET.SubElement(root_node, "mxCell", id="1", parent="0")
    
    lifelines = [
        ("Patient", "Patient", 40),
        ("Auth", "AuthenticatedSessionController", 220),
        ("Controller", "PatientFollowUpController", 440),
        ("Triage", "TriageClassificationService", 680),
        ("DB", "PostgreSQL DB", 880),
        ("Audit", "AuditLogService", 1060),
        ("Queue", "ProcessFollowUpSubmission", 1240),
        ("API", "OpenMRS API", 1420),
    ]
    
    for obj_id, name, x in lifelines:
        cell = ET.SubElement(root_node, "mxCell", id=obj_id, value=name, style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;", vertex="1", parent="1")
        ET.SubElement(cell, "mxGeometry", x=str(x), y="40", width="160", height="900", **{"as": "geometry"})
        
    messages = [
        ("Patient", "Auth", "1. POST /login (credentials)", False),
        ("Auth", "DB", "2. Verify credentials", False),
        ("DB", "Auth", "3. Return User", True),
        ("Auth", "Patient", "4. 302 Redirect (Set-Cookie: session)", True),
        
        ("Patient", "Controller", "5. GET /patient/followup/create (Cookie)", False),
        ("Controller", "Patient", "6. 200 OK (Render Form)", True),
        
        ("Patient", "Controller", "7. POST /patient/followup (symptoms, etc)", False),
        ("Controller", "DB", "8. FollowUpSubmission::create()", False),
        ("DB", "Controller", "9. Return record", True),
        ("Controller", "Triage", "10. classify(submission)", False),
        ("Triage", "Controller", "11. Return urgencyLevel", True),
        ("Controller", "DB", "12. update urgency_level", False),
        ("Controller", "Audit", "13. log('submission_created')", False),
        ("Audit", "DB", "14. insert into audit_logs", False),
        ("Controller", "Queue", "15. dispatch(submission)", False),
        ("Controller", "Patient", "16. 302 Redirect to Confirmation", True),
        ("Queue", "API", "17. POST /api/emr/observations", False),
        ("API", "Queue", "18. [Success] 201 Created", True),
        ("Queue", "DB", "19. update sync_status='Synced'", False),
        ("Queue", "Audit", "20. log('sync_complete')", False),
    ]
    
    y = 100
    msg_idx = 100
    for src, dst, text, dashed in messages:
        style = "html=1;verticalAlign=bottom;endArrow=block;edgeStyle=elbowEdgeStyle;elbow=vertical;curved=0;rounded=0;"
        if dashed:
            style += "dashed=1;"
            
        src_x = next(l[2] for l in lifelines if l[0] == src) + 80
        dst_x = next(l[2] for l in lifelines if l[0] == dst) + 80
        
        cell = ET.SubElement(root_node, "mxCell", id=str(msg_idx), value=text, style=style, edge="1", parent="1")
        geo = ET.SubElement(cell, "mxGeometry", width="80", relative="1", **{"as": "geometry"})
        
        # Source point
        ET.SubElement(geo, "mxPoint", x=str(src_x), y=str(y), **{"as": "sourcePoint"})
        # Target point
        ET.SubElement(geo, "mxPoint", x=str(dst_x), y=str(y), **{"as": "targetPoint"})
        
        y += 40
        msg_idx += 1
        
    xmlstr = xml.dom.minidom.parseString(ET.tostring(root)).toprettyxml(indent="  ")
    
    with open("drawio_sequence_full.xml", "w") as f:
        f.write(xmlstr)

if __name__ == "__main__":
    generate_drawio_xml()
